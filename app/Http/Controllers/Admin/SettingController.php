<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    private array $defaultSettings = [
        'general' => ['site_name', 'site_tagline', 'author_name', 'author_bio', 'author_avatar', 'contact_email'],
        'amazon' => ['affiliate_tag', 'default_country'],
        'seo' => ['default_og_image', 'google_analytics_id', 'google_search_console'],
        'social' => ['facebook_url', 'instagram_url', 'pinterest_url', 'youtube_url'],
    ];

    public function index(): View
    {
        $settings = [];
        foreach ($this->defaultSettings as $group => $keys) {
            foreach ($keys as $key) {
                $settings[$group][$key] = Setting::get("{$group}.{$key}");
            }
        }
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'general.site_name' => ['nullable', 'string', 'max:255'],
            'general.author_name' => ['nullable', 'string', 'max:255'],
            'amazon.affiliate_tag' => ['nullable', 'string', 'max:100'],
            'seo.google_analytics_id' => ['nullable', 'string', 'max:50'],
        ]);

        foreach ($this->defaultSettings as $group => $keys) {
            foreach ($keys as $key) {
                $value = $request->input("{$group}.{$key}");
                if ($value !== null) {
                    Setting::set("{$group}.{$key}", $value, $group);
                }
            }
        }

        return back()->with('success', '¡Configuración guardada exitosamente!');
    }
}
