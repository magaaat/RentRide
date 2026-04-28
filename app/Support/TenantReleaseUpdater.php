<?php

namespace App\Support;

use RuntimeException;
use Symfony\Component\Process\Process;

class TenantReleaseUpdater
{
    public function applyTag(string $tag, ?int $tenantId = null): array
    {
        if (! (bool) config('rentride.tenant_release_updater_enabled', false)) {
            throw new RuntimeException('Tenant release updater is disabled. Set TENANT_RELEASE_UPDATER_ENABLED=true.');
        }

        $tag = trim($tag);
        if ($tag === '' || ! preg_match('/^[A-Za-z0-9._\-\/]+$/', $tag)) {
            throw new RuntimeException('Invalid release tag received from GitHub.');
        }

        $basePath = base_path();
        $timeout = max(30, (int) config('rentride.tenant_release_updater_timeout_seconds', 300));
        $composer = trim((string) config('rentride.tenant_release_updater_composer_command', 'composer'));
        $php = trim((string) config('rentride.tenant_release_updater_php_command', 'php'));
        $git = trim((string) config('rentride.tenant_release_updater_git_command', 'git'));
        if ($git === '') {
            $git = 'git';
        }
        if (($php === '' || $php === 'php') && PHP_BINARY !== '') {
            $php = PHP_BINARY;
        }

        $steps = [];

        $steps[] = $this->run([$git, 'rev-parse', '--is-inside-work-tree'], $basePath, $timeout, 'Validate git repository', null);
        $status = $this->run([$git, 'status', '--porcelain'], $basePath, $timeout, 'Check working tree state', null);
        if (trim($status['output']) !== '') {
            throw new RuntimeException('Update blocked: working tree has local changes. Commit or stash them first.');
        }

        // If the tag already exists locally (e.g. fetched manually), don't fail the update on network hiccups.
        if (! $this->tagExistsLocally($git, $basePath, $timeout, $tag)) {
            $steps[] = $this->fetchLatestTagsWithRetry($git, $basePath, $timeout);
        } else {
            $steps[] = [
                'label' => 'Fetch latest tags',
                'command' => 'git fetch --tags origin',
                'output' => 'Skipped: target tag already exists locally.',
            ];
        }
        $steps[] = $this->run([$git, 'rev-parse', '-q', '--verify', "refs/tags/{$tag}"], $basePath, $timeout, 'Verify release tag exists', null);
        $steps[] = $this->run([$git, 'checkout', '--force', $tag], $basePath, $timeout, 'Checkout release tag', null);
        $steps[] = $this->run([$composer, 'install', '--no-interaction', '--prefer-dist', '--optimize-autoloader'], $basePath, $timeout, 'Install PHP dependencies', null);
        $steps[] = $this->run([$php, 'artisan', 'migrate', '--force'], $basePath, $timeout, 'Run database migrations', null);
        $steps[] = $this->runTenantMigrations($php, $basePath, $timeout, $tenantId);
        $steps[] = $this->run([$php, 'artisan', 'optimize:clear'], $basePath, $timeout, 'Clear application caches', null);

        return [
            'tag' => $tag,
            'steps' => $steps,
        ];
    }

    protected function runTenantMigrations(string $php, string $basePath, int $timeout, ?int $tenantId): array
    {
        if ($tenantId !== null && $tenantId > 0) {
            return $this->run(
                [$php, 'artisan', 'tenants:migrate', '--tenants=' . $tenantId, '--force'],
                $basePath,
                $timeout,
                'Run tenant migrations',
                'php artisan tenants:migrate --tenants=' . $tenantId . ' --force'
            );
        }

        return $this->run(
            [$php, 'artisan', 'tenants:migrate', '--force'],
            $basePath,
            $timeout,
            'Run tenant migrations',
            'php artisan tenants:migrate --force'
        );
    }

    protected function run(array $command, string $cwd, int $timeout, string $label, ?string $displayCommand = null): array
    {
        $process = new Process($command, $cwd, $this->subprocessEnvironment());
        $process->setTimeout($timeout);
        $this->ensureChildProcessesSeePhpOnPath($process);
        $process->run();

        if (! $process->isSuccessful()) {
            $output = trim($process->getOutput() . "\n" . $process->getErrorOutput());
            $exit = $process->getExitCode();
            $detail = $output !== ''
                ? $output
                : sprintf('(no stdout/stderr; exit code %s)', $exit === null ? 'null' : (string) $exit);

            throw new RuntimeException($label . ' failed: ' . $detail);
        }

        return [
            'label' => $label,
            'command' => $displayCommand ?? implode(' ', $command),
            'output' => trim($process->getOutput()),
        ];
    }

    protected function fetchLatestTagsWithRetry(string $git, string $cwd, int $timeout): array
    {
        $attempts = 3;
        $lastError = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                return $this->run(
                    $this->gitFetchTagsCommand($git),
                    $cwd,
                    $timeout,
                    'Fetch latest tags',
                    'git fetch --tags origin'
                );
            } catch (RuntimeException $e) {
                $lastError = $e->getMessage();
                if ($attempt < $attempts) {
                    usleep(400_000);
                }
            }
        }

        throw new RuntimeException($lastError ?? 'Fetch latest tags failed.');
    }

    protected function tagExistsLocally(string $git, string $cwd, int $timeout, string $tag): bool
    {
        try {
            $this->run([$git, 'rev-parse', '-q', '--verify', "refs/tags/{$tag}"], $cwd, $timeout, 'Check local tag', null);
            return true;
        } catch (RuntimeException) {
            return false;
        }
    }

    /**
     * Private repos need credentials in non-interactive PHP; the API token is reused as an HTTPS header.
     *
     * @return list<string>
     */
    protected function gitFetchTagsCommand(string $git): array
    {
        $token = trim((string) config('rentride.github_token', ''));
        if ($token === '') {
            return [$git, 'fetch', '--tags', 'origin'];
        }

        $basic = base64_encode('x-access-token:'.$token);

        return [
            $git,
            '-c',
            'http.version=HTTP/1.1',
            '-c',
            'http.extraheader=AUTHORIZATION: Basic '.$basic,
            'fetch',
            '--tags',
            'origin',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function subprocessEnvironment(): array
    {
        $base = array_merge($_ENV, $_SERVER);
        $env = [];

        foreach ($base as $key => $value) {
            if (! is_string($key) || $key === '') {
                continue;
            }
            if (is_string($value) || is_int($value) || is_float($value)) {
                $env[$key] = (string) $value;
            }
        }

        if (PHP_OS_FAMILY === 'Windows') {
            foreach ([
                'SystemRoot',
                'WINDIR',
                'windir',
                'ALLUSERSPROFILE',
                'PUBLIC',
                'ProgramFiles',
                'ProgramFiles(x86)',
                'LOCALAPPDATA',
                'USERPROFILE',
                'APPDATA',
                'TMP',
                'TEMP',
            ] as $name) {
                $v = getenv($name);
                if (is_string($v) && $v !== '') {
                    $env[$name] = $v;
                }
            }

            $path = $env['Path'] ?? $env['PATH'] ?? '';
            if ($path === '') {
                $pathCandidate = getenv('Path');
                if (is_string($pathCandidate) && $pathCandidate !== '') {
                    $path = $pathCandidate;
                } else {
                    $pathCandidate = getenv('PATH');
                    if (is_string($pathCandidate) && $pathCandidate !== '') {
                        $path = $pathCandidate;
                    }
                }
            }
            if ($path !== '') {
                $env['PATH'] = $path;
                $env['Path'] = $path;
            }

            $userProfile = $env['USERPROFILE'] ?? getenv('USERPROFILE');
            if (! is_string($userProfile) || $userProfile === '') {
                $username = getenv('USERNAME');
                $userProfile = 'C:\\Users\\'.(is_string($username) && $username !== '' ? $username : 'Default');
            }
            $userProfile = rtrim($userProfile, '\\/');

            $appData = $env['APPDATA'] ?? getenv('APPDATA') ?: $userProfile.'\\AppData\\Roaming';
            $composerHome = $env['COMPOSER_HOME'] ?? getenv('COMPOSER_HOME') ?: rtrim((string) $appData, '\\/').'\\Composer';
            $home = $env['HOME'] ?? getenv('HOME') ?: $userProfile;

            $localAppData = $env['LOCALAPPDATA'] ?? getenv('LOCALAPPDATA') ?: $userProfile.'\\AppData\\Local';
            $temp = $env['TEMP'] ?? getenv('TEMP') ?: rtrim((string) $localAppData, '\\/').'\\Temp';
            $tmp = $env['TMP'] ?? getenv('TMP') ?: $temp;

            $env['USERPROFILE'] = $userProfile;
            $env['APPDATA'] = (string) $appData;
            $env['COMPOSER_HOME'] = (string) $composerHome;
            $env['HOME'] = (string) $home;
            $env['LOCALAPPDATA'] = (string) $localAppData;
            $env['TEMP'] = (string) $temp;
            $env['TMP'] = (string) $tmp;
        }

        $env['GIT_TERMINAL_PROMPT'] = '0';
        if (PHP_OS_FAMILY === 'Windows') {
            $env['GCM_INTERACTIVE'] = 'Never';
        }

        return $env;
    }

    /**
     * composer.bat calls bare php; web SAPI subprocesses often lack PHP on PATH (Windows).
     */
    protected function ensureChildProcessesSeePhpOnPath(Process $process): void
    {
        $phpBinary = PHP_BINARY;
        if ($phpBinary === '' || ! is_file($phpBinary)) {
            return;
        }

        $phpDir = dirname($phpBinary);
        if ($phpDir === '' || ! is_dir($phpDir)) {
            return;
        }

        $sep = PATH_SEPARATOR;
        $path = getenv('PATH');
        if ($path === false || $path === '') {
            $path = getenv('Path');
        }
        $path = is_string($path) ? $path : '';
        $prefix = $phpDir.$sep;
        if ($path !== '' && (str_starts_with($path, $phpDir.$sep) || str_starts_with($path, $phpDir.';'))) {
            return;
        }

        $merged = $prefix.$path;
        $env = $process->getEnv();
        $env['PATH'] = $merged;
        $env['Path'] = $merged;
        $process->setEnv($env);
    }
}
