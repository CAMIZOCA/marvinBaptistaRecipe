<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-6'),
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4.1-mini'),
        'api_url' => env('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions'),
        'timeout' => (int) env('OPENAI_TIMEOUT', 90),
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        // OpenAI-compatible endpoint from Google AI Studio
        'api_url' => env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions'),
        'timeout' => (int) env('GEMINI_TIMEOUT', 90),
    ],

    'gemma' => [
        'key' => env('GEMMA_API_KEY', 'local'),
        'model' => env('GEMMA_MODEL', 'gemma3:4b'),
        // Default local OpenAI-compatible server (Ollama/LM Studio/Jan)
        'api_url' => env('GEMMA_API_URL', 'http://localhost:11434/v1/chat/completions'),
        'timeout' => (int) env('GEMMA_TIMEOUT', 300),
    ],

    'deepinfra' => [
        'key' => env('DEEPINFRA_API_KEY'),
        'model' => env('DEEPINFRA_MODEL', 'meta-llama/Llama-3.3-70B-Instruct'),
        // OpenAI-compatible endpoint
        'api_url' => env('DEEPINFRA_API_URL', 'https://api.deepinfra.com/v1/openai/chat/completions'),
        'timeout' => (int) env('DEEPINFRA_TIMEOUT', 180),
    ],

    'amazon' => [
        'affiliate_tag' => env('AMAZON_AFFILIATE_TAG', ''),
        'default_country' => env('AMAZON_DEFAULT_COUNTRY', 'US'),
    ],

];
