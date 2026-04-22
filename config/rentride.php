<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Display version (not from Git automatically — set in .env or VERSION file)
    |--------------------------------------------------------------------------
    |
    | For production, set APP_VERSION when you deploy (often from CI using a Git
    | tag, e.g. v1.2.0). Optional APP_RELEASE_GIT_SHA is the short commit hash.
    |
    */
    'version' => env('APP_VERSION', trim((string) @file_get_contents(base_path('VERSION'))) ?: 'dev'),

    'git_sha' => env('APP_RELEASE_GIT_SHA', ''),

    /*
    |--------------------------------------------------------------------------
    | Links shown on About & support (GitHub Releases page is the usual URL)
    |--------------------------------------------------------------------------
    */
    'github_repo_url' => env('GITHUB_REPO_URL', ''),
    'github_token' => env('GITHUB_TOKEN', ''),
    'github_release_cache_minutes' => (int) env('GITHUB_RELEASE_CACHE_MINUTES', 30),
    'github_release_timeout_seconds' => (int) env('GITHUB_RELEASE_TIMEOUT_SECONDS', 5),
    'github_release_verify_ssl' => (bool) env('GITHUB_RELEASE_VERIFY_SSL', true),
    'update_test_module_min_version' => env('UPDATE_TEST_MODULE_MIN_VERSION', 'v1.0.3'),
    'tenant_release_updater_enabled' => (bool) env('TENANT_RELEASE_UPDATER_ENABLED', false),
    'tenant_release_updater_timeout_seconds' => (int) env('TENANT_RELEASE_UPDATER_TIMEOUT_SECONDS', 300),
    'tenant_release_updater_composer_command' => env('TENANT_RELEASE_UPDATER_COMPOSER_COMMAND', 'composer'),
    'tenant_release_updater_php_command' => env('TENANT_RELEASE_UPDATER_PHP_COMMAND', 'php'),

    'support_email' => env('SUPPORT_EMAIL', ''),

];
