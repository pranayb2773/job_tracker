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
];
