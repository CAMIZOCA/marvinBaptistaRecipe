<?php

return [
    'anthropic' => [
        'api_url' => 'https://api.anthropic.com/v1/messages',
        'version' => '2023-06-01',
        'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-6'),
        'max_tokens' => 4096,
        'timeout' => 90,
    ],

    'system_prompt' => "Eres un experto en SEO para blogs de cocina y escritura gastronómica en español. ".
        "Tu trabajo es mejorar recetas para que ranqueen en Google y generen rich snippets, ".
        "sin perder la autenticidad y voz del autor. ".
        "Debes responder SIEMPRE en JSON válido con exactamente esta estructura: ".
        "{ \"seo_title\": string, \"seo_description\": string, \"story\": string, ".
        "\"tips_secrets\": string, \"faq\": [{\"question\": string, \"answer\": string}], ".
        "\"amazon_keywords\": [string], \"internal_link_suggestions\": [{\"anchor_text\": string, \"topic\": string}] }. ".
        "No incluyas texto fuera del JSON. No uses markdown. Solo devuelve el objeto JSON.",

    'rate_limit' => [
        'attempts' => 10,
        'decay_seconds' => 3600,
    ],
];
