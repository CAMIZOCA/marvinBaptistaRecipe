<?php

return [
    'anthropic' => [
        'api_url' => 'https://api.anthropic.com/v1/messages',
        'version' => '2023-06-01',
        'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-6'),
        'max_tokens' => 6000,
        'timeout' => 90,
    ],

    'openai' => [
        'api_url' => env('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions'),
        'model' => env('OPENAI_MODEL', 'gpt-4.1-mini'),
        'max_tokens' => 4096,
        'timeout' => (int) env('OPENAI_TIMEOUT', 90),
    ],

    'gemini' => [
        // OpenAI-compatible endpoint from Google AI Studio
        'api_url' => env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'max_tokens' => 4096,
        'timeout' => (int) env('GEMINI_TIMEOUT', 90),
    ],

    'gemma' => [
        // OpenAI-compatible endpoint (often local)
        'api_url' => env('GEMMA_API_URL', 'http://localhost:11434/v1/chat/completions'),
        'model' => env('GEMMA_MODEL', 'gemma3:4b'),
        'max_tokens' => 4096,
        'timeout' => (int) env('GEMMA_TIMEOUT', 300),
    ],

    'deepinfra' => [
        // OpenAI-compatible endpoint (DeepInfra)
        'api_url' => env('DEEPINFRA_API_URL', 'https://api.deepinfra.com/v1/openai/chat/completions'),
        'model' => env('DEEPINFRA_MODEL', 'meta-llama/Llama-3.3-70B-Instruct'),
        'max_tokens' => 4096,
        'timeout' => (int) env('DEEPINFRA_TIMEOUT', 180),
    ],

    'system_prompt' =>
        "Eres un chef internacional con 20 años de experiencia en cocina latinoamericana, mediterránea y asiática, " .
        "además de editor gastronómico senior para publicaciones de alta circulación. " .
        "Escribes con autoridad, calidez y precisión para lectores que descubren un plato por primera vez. " .
        "Tu contenido cumple los estándares E-E-A-T de Google: experiencia demostrable, conocimiento experto, " .
        "autoridad temática y confiabilidad factual. " .
        "REGLAS ABSOLUTAS: " .
        "1. Responde ÚNICAMENTE con un objeto JSON válido, sin ningún texto antes ni después. " .
        "2. Nunca uses bloques markdown (``` o similar). " .
        "3. Todos los campos del JSON son obligatorios y NUNCA pueden ser null ni estar vacíos. " .
        "4. Cada palabra en español debe tener su separación correcta: no pegues palabras ni omitas espacios. " .
        "5. No uses caracteres especiales raros, llaves, corchetes ni marcadores dentro de los textos como {#}, [#], etc. " .
        "6. El JSON final debe pasar JSON.parse() sin errores. " .
        "ESTRUCTURA EXACTA que debes devolver (sin variaciones): " .
        "{\"seo_title\":\"string\",\"seo_description\":\"string\",\"story\":\"string\"," .
        "\"tips_secrets\":\"string\",\"faq\":[{\"question\":\"string\",\"answer\":\"string\"}]," .
        "\"amazon_keywords\":[\"string\"],\"internal_link_suggestions\":[\"string\"]}",

    'rate_limit' => [
        'attempts' => 10,
        'decay_seconds' => 3600,
    ],
];
