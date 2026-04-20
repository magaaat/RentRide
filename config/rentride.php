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

    'support_email' => env('SUPPORT_EMAIL', ''),

];
