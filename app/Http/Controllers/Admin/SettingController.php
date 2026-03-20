<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /** Flat key → group mapping */
    private const GROUPS = [
        'site_name'               => 'general',
        'site_tagline'            => 'general',
        'site_url'                => 'general',
        'contact_email'           => 'general',
        'logo_url'                => 'general',
        'recipes_per_page'        => 'general',
        'maintenance_mode'        => 'general',

        'amazon_tag_us'           => 'amazon',
        'amazon_tag_mx'           => 'amazon',
        'amazon_tag_es'           => 'amazon',
        'amazon_tag_ar'           => 'amazon',
        'affiliate_disclaimer'    => 'amazon',

        'default_meta_title'      => 'seo',
        'default_meta_description'=> 'seo',
        'google_analytics_id'     => 'seo',
        'google_search_console'   => 'seo',
        'default_og_image'        => 'seo',
        'ga4_property_id'         => 'seo',
        'search_console_site_url' => 'seo',

        'social_instagram'        => 'social',
        'social_youtube'          => 'social',
        'social_facebook'         => 'social',
        'social_tiktok'           => 'social',
        'social_pinterest'        => 'social',
        'social_twitter'          => 'social',

        'anthropic_api_key'       => 'ai',
        'anthropic_model'         => 'ai',
        'openai_api_key'          => 'ai',
        'openai_model'            => 'ai',
        'gemini_api_key'          => 'ai',
        'gemini_model'            => 'ai',
        'gemma_api_key'           => 'ai',
        'gemma_api_url'           => 'ai',
        'gemma_model'             => 'ai',
        'gemma_timeout'           => 'ai',
        'deepinfra_api_key'       => 'ai',
        'deepinfra_api_url'       => 'ai',
        'deepinfra_model'         => 'ai',
        'deepinfra_timeout'       => 'ai',

        'ai_provider'             => 'ai',   // 'anthropic' | 'openai' | 'gemini' | 'gemma' | 'deepinfra' | 'local'
        'local_ai_url'            => 'ai',
        'local_ai_model'          => 'ai',
        'local_ai_api_key'        => 'ai',
        'local_ai_timeout'        => 'ai',

        // Per-field prompt customisation (optional overrides — empty = use default)
        'ai_prompt_seo_title'        => 'ai',
        'ai_prompt_seo_description'  => 'ai',
        'ai_prompt_story'            => 'ai',
        'ai_prompt_tips_secrets'     => 'ai',
        'ai_prompt_faq'              => 'ai',
        'ai_prompt_amazon_keywords'  => 'ai',
        'ai_prompt_internal_links'   => 'ai',
    ];

    public function index(): View
    {
        $settings = [];
        foreach (array_keys(self::GROUPS) as $key) {
            $settings[$key] = Setting::get($key);
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->input('settings', []);

        foreach ($data as $key => $value) {
            if (!array_key_exists($key, self::GROUPS)) {
                continue;
            }

            // Don't overwrite secret keys with empty value
            if (in_array($key, [
                'anthropic_api_key',
                'openai_api_key',
                'gemini_api_key',
                'gemma_api_key',
                'deepinfra_api_key',
                'local_ai_api_key',
            ], true) && blank($value)) {
                continue;
            }

            Setting::set($key, $value ?? '', self::GROUPS[$key]);
        }

        return back()->with('success', '¡Configuración guardada exitosamente!');
    }
}
