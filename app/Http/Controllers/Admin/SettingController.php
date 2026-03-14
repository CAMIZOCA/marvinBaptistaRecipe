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

        'social_instagram'        => 'social',
        'social_youtube'          => 'social',
        'social_facebook'         => 'social',
        'social_tiktok'           => 'social',
        'social_pinterest'        => 'social',
        'social_twitter'          => 'social',

        'anthropic_api_key'       => 'ai',
        'anthropic_model'         => 'ai',
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

            // For API key: skip if blank (don't overwrite with empty)
            if ($key === 'anthropic_api_key' && blank($value)) {
                continue;
            }

            Setting::set($key, $value ?? '', self::GROUPS[$key]);
        }

        return back()->with('success', '¡Configuración guardada exitosamente!');
    }
}
