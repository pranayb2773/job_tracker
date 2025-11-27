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

    'providers' => [
        'gemini' => [
            'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
            'timeout' => (int) env('AI_TIMEOUT', 180),
            'max_tokens' => (int) env('AI_MAX_TOKENS', 8000),
        ],
        'claude' => [
            'model' => env('CLAUDE_MODEL', 'claude-sonnet-4-5-20250929'),
            'timeout' => (int) env('AI_TIMEOUT', 180),
            'max_tokens' => (int) env('AI_MAX_TOKENS', 8000),
        ],
        'openai' => [
            'model' => env('OPENAI_MODEL', 'gpt-4o'),
            'timeout' => (int) env('AI_TIMEOUT', 180),
            'max_tokens' => (int) env('AI_MAX_TOKENS', 8000),
        ],
        'groq' => [
            'model' => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
            'timeout' => (int) env('AI_TIMEOUT', 180),
            'max_tokens' => (int) env('AI_MAX_TOKENS', 8000),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Analysis Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for AI-powered analysis (CV/Resume and Job Roles).
    |
    */

    'ai_analysis' => [
        /*
        | Default AI provider for analysis.
        | Supported: 'gemini', 'claude', 'openai', 'groq'
        */
        'default_provider' => env('AI_ANALYSIS_PROVIDER', 'gemini'),

        /*
        | Rate limiting configuration
        */
        'rate_limit' => [
            'daily_limit' => (int) env('AI_ANALYSIS_DAILY_LIMIT', 30),
        ],
    ],
];
