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

        $steps = [];

        $steps[] = $this->run(['git', 'rev-parse', '--is-inside-work-tree'], $basePath, $timeout, 'Validate git repository');
        $status = $this->run(['git', 'status', '--porcelain'], $basePath, $timeout, 'Check working tree state');
        if (trim($status['output']) !== '') {
            throw new RuntimeException('Update blocked: working tree has local changes. Commit or stash them first.');
        }

        $steps[] = $this->run(['git', 'fetch', '--tags', 'origin'], $basePath, $timeout, 'Fetch latest tags');
        $steps[] = $this->run(['git', 'rev-parse', '-q', '--verify', "refs/tags/{$tag}"], $basePath, $timeout, 'Verify release tag exists');
        $steps[] = $this->run(['git', 'checkout', '--force', $tag], $basePath, $timeout, 'Checkout release tag');
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
        $process = new Process($command, $cwd);
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
}
