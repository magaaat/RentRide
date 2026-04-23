<?php

namespace App\Support;

use RuntimeException;
use Symfony\Component\Process\Process;

class TenantReleaseUpdater
{
    public function applyTag(string $tag): array
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

        $steps = [];

        $steps[] = $this->run([$git, 'rev-parse', '--is-inside-work-tree'], $basePath, $timeout, 'Validate git repository');
        $status = $this->run([$git, 'status', '--porcelain'], $basePath, $timeout, 'Check working tree state');
        if (trim($status['output']) !== '') {
            throw new RuntimeException('Update blocked: working tree has local changes. Commit or stash them first.');
        }

        $steps[] = $this->run([$git, 'fetch', '--tags', 'origin'], $basePath, $timeout, 'Fetch latest tags');
        $steps[] = $this->run([$git, 'rev-parse', '-q', '--verify', "refs/tags/{$tag}"], $basePath, $timeout, 'Verify release tag exists');
        $steps[] = $this->run([$git, 'checkout', '--force', $tag], $basePath, $timeout, 'Checkout release tag');
        $steps[] = $this->run([$composer, 'install', '--no-interaction', '--prefer-dist', '--optimize-autoloader'], $basePath, $timeout, 'Install PHP dependencies');
        $steps[] = $this->run([$php, 'artisan', 'migrate', '--force'], $basePath, $timeout, 'Run database migrations');
        $steps[] = $this->run([$php, 'artisan', 'optimize:clear'], $basePath, $timeout, 'Clear application caches');

        return [
            'tag' => $tag,
            'steps' => $steps,
        ];
    }

    protected function run(array $command, string $cwd, int $timeout, string $label): array
    {
        $process = new Process($command, $cwd, $this->subprocessEnvironment());
        $process->setTimeout($timeout);
        $process->run();

        if (! $process->isSuccessful()) {
            $output = trim($process->getOutput() . "\n" . $process->getErrorOutput());
            throw new RuntimeException($label . ' failed: ' . ($output !== '' ? $output : 'no command output'));
        }

        return [
            'label' => $label,
            'command' => implode(' ', $command),
            'output' => trim($process->getOutput()),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function subprocessEnvironment(): array
    {
        $env = [];

        foreach (array_merge($_ENV, $_SERVER) as $key => $value) {
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
        }

        $env['GIT_TERMINAL_PROMPT'] = '0';

        return $env;
    }
}
