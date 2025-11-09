<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | CV Analysis Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for AI-powered CV/Resume analysis.
    |
    */

    'cv_analysis' => [
        /*
        | Default AI provider for CV analysis.
        | Supported: 'gemini', 'claude', 'openai'
        */
        'default_provider' => env('CV_ANALYSIS_PROVIDER', 'gemini'),

        /*
        | Rate limiting configuration
        */
        'rate_limit' => [
            'daily_limit' => (int)env('CV_ANALYSIS_DAILY_LIMIT', 10),
        ],

        /*
        | Provider-specific configuration
        */
        'providers' => [
            'gemini' => [
                'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
                'timeout' => (int)env('AI_TIMEOUT', 180),
                'max_tokens' => (int)env('AI_MAX_TOKENS', 4000),
            ],
            'claude' => [
                'model' => env('CLAUDE_MODEL', 'claude-sonnet-4-5-20250929'),
                'timeout' => (int)env('AI_TIMEOUT', 180),
                'max_tokens' => (int)env('AI_MAX_TOKENS', 4000),
            ],
            'openai' => [
                'model' => env('OPENAI_MODEL', 'gpt-4o'),
                'timeout' => (int)env('AI_TIMEOUT', 180),
                'max_tokens' => (int)env('AI_MAX_TOKENS', 4000),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Analysis Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for AI-powered job description role analysis.
    |
    */

    'role_analysis' => [
        /*
        | Default AI provider for role analysis.
        | Supported: 'gemini', 'claude', 'openai'
        */
        'default_provider' => env('ROLE_ANALYSIS_PROVIDER', env('CV_ANALYSIS_PROVIDER', 'gemini')),

        /*
        | Rate limiting configuration
        */
        'rate_limit' => [
            'daily_limit' => (int)env('ROLE_ANALYSIS_DAILY_LIMIT', 20),
        ],

        /*
        | AI configuration for role analysis
        | Role analysis requires more tokens and time due to comprehensive output format
        */
        'timeout' => (int)env('ROLE_ANALYSIS_TIMEOUT', 240),
        'max_tokens' => (int)env('ROLE_ANALYSIS_MAX_TOKENS', 8000),
    ],
];
