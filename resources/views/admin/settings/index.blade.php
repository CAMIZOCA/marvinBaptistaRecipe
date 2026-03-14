@extends('admin.layouts.app')

@section('page-title', 'Ajustes')

@section('content')
<div class="p-6 space-y-6 max-w-4xl">

    <div>
        <h1 class="text-2xl font-bold text-zinc-100">Ajustes</h1>
        <p class="text-zinc-400 text-sm mt-1">Configuración general del sitio</p>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-900/50 border border-emerald-700 rounded-xl text-emerald-300 text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Settings Tabs --}}
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden">
        <div class="flex border-b border-zinc-700 overflow-x-auto" id="settings-tab-nav">
            @foreach([
                ['id' => 'general',  'label' => 'General'],
                ['id' => 'amazon',   'label' => 'Amazon'],
                ['id' => 'seo',      'label' => 'SEO'],
                ['id' => 'social',   'label' => 'Redes Sociales'],
            ] as $tab)
            <button type="button"
                    class="settings-tab flex-shrink-0 px-6 py-3.5 text-sm font-medium transition-colors border-b-2 focus:outline-none"
                    data-settings-tab="{{ $tab['id'] }}">
                {{ $tab['label'] }}
            </button>
            @endforeach
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-6">
            @csrf
            @method('PUT')

            {{-- ==================== GENERAL ==================== --}}
            <div id="settings-panel-general" class="settings-panel space-y-5">
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Nombre del Sitio</label>
                        <input type="text" name="settings[site_name]"
                               value="{{ old('settings.site_name', $settings['site_name'] ?? 'Marvin Baptista') }}"
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Tagline</label>
                        <input type="text" name="settings[site_tagline]"
                               value="{{ old('settings.site_tagline', $settings['site_tagline'] ?? '') }}"
                               placeholder="Recetas con sabor a casa"
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">URL del Sitio</label>
                        <input type="url" name="settings[site_url]"
                               value="{{ old('settings.site_url', $settings['site_url'] ?? config('app.url')) }}"
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Email de Contacto</label>
                        <input type="email" name="settings[contact_email]"
                               value="{{ old('settings.contact_email', $settings['contact_email'] ?? '') }}"
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Logo URL</label>
                        <input type="url" name="settings[logo_url]"
                               value="{{ old('settings.logo_url', $settings['logo_url'] ?? '') }}"
                               placeholder="https://..."
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Recetas por página</label>
                        <input type="number" name="settings[recipes_per_page]" min="6" max="48" step="6"
                               value="{{ old('settings.recipes_per_page', $settings['recipes_per_page'] ?? 12) }}"
                               class="w-40 px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="hidden" name="settings[maintenance_mode]" value="0">
                                <input type="checkbox" name="settings[maintenance_mode]" value="1"
                                       {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-zinc-600 peer-focus:ring-2 peer-focus:ring-amber-500 rounded-full peer peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-zinc-300">Modo Mantenimiento</span>
                                <p class="text-xs text-zinc-500">Muestra una página de mantenimiento a los visitantes</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ==================== AMAZON ==================== --}}
            <div id="settings-panel-amazon" class="settings-panel hidden space-y-5">
                <div class="p-4 bg-amber-900/20 border border-amber-700/40 rounded-xl text-amber-300 text-sm">
                    <p class="font-medium mb-1">Programa de Afiliados de Amazon</p>
                    <p>Los tags se añaden automáticamente a todas las URLs de Amazon en el sitio.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-5">
                    @foreach([
                        ['key' => 'amazon_tag_us', 'label' => '🇺🇸 Tag EE.UU. (.com)', 'placeholder' => 'sitio-20'],
                        ['key' => 'amazon_tag_mx', 'label' => '🇲🇽 Tag México (.com.mx)', 'placeholder' => 'sitio-mx-20'],
                        ['key' => 'amazon_tag_es', 'label' => '🇪🇸 Tag España (.es)', 'placeholder' => 'sitio-es-20'],
                        ['key' => 'amazon_tag_ar', 'label' => '🇦🇷 Tag Argentina (.com.ar)', 'placeholder' => 'sitio-ar-20'],
                    ] as $field)
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">{{ $field['label'] }}</label>
                        <input type="text" name="settings[{{ $field['key'] }}]"
                               value="{{ old('settings.'.$field['key'], $settings[$field['key']] ?? '') }}"
                               placeholder="{{ $field['placeholder'] }}"
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    @endforeach
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-1.5">Texto de Aviso de Afiliado</label>
                    <textarea name="settings[affiliate_disclaimer]" rows="3"
                              class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none">{{ old('settings.affiliate_disclaimer', $settings['affiliate_disclaimer'] ?? 'Como afiliado de Amazon, recibo una pequeña comisión por las compras realizadas a través de mis enlaces, sin costo adicional para ti.') }}</textarea>
                </div>
            </div>

            {{-- ==================== SEO ==================== --}}
            <div id="settings-panel-seo" class="settings-panel hidden space-y-5">
                <div class="grid md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Meta Title por defecto</label>
                        <input type="text" name="settings[default_meta_title]"
                               value="{{ old('settings.default_meta_title', $settings['default_meta_title'] ?? '') }}"
                               maxlength="60"
                               placeholder="Marvin Baptista | Recetas Auténticas"
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Meta Description por defecto</label>
                        <textarea name="settings[default_meta_description]" rows="2" maxlength="160"
                                  placeholder="Recetas auténticas de cocina latinoamericana y mediterránea..."
                                  class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none">{{ old('settings.default_meta_description', $settings['default_meta_description'] ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Google Analytics ID</label>
                        <input type="text" name="settings[google_analytics_id]"
                               value="{{ old('settings.google_analytics_id', $settings['google_analytics_id'] ?? '') }}"
                               placeholder="G-XXXXXXXXXX"
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Google Search Console</label>
                        <input type="text" name="settings[google_search_console]"
                               value="{{ old('settings.google_search_console', $settings['google_search_console'] ?? '') }}"
                               placeholder="código de verificación"
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">OG Image por defecto</label>
                        <input type="url" name="settings[default_og_image]"
                               value="{{ old('settings.default_og_image', $settings['default_og_image'] ?? '') }}"
                               placeholder="https://..."
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
            </div>

            {{-- ==================== SOCIAL ==================== --}}
            <div id="settings-panel-social" class="settings-panel hidden space-y-5">
                <div class="grid md:grid-cols-2 gap-5">
                    @foreach([
                        ['key' => 'social_instagram', 'label' => 'Instagram', 'placeholder' => 'https://instagram.com/...', 'icon' => 'instagram'],
                        ['key' => 'social_youtube',   'label' => 'YouTube',   'placeholder' => 'https://youtube.com/...', 'icon' => 'youtube'],
                        ['key' => 'social_facebook',  'label' => 'Facebook',  'placeholder' => 'https://facebook.com/...', 'icon' => 'facebook'],
                        ['key' => 'social_tiktok',    'label' => 'TikTok',    'placeholder' => 'https://tiktok.com/@...', 'icon' => 'tiktok'],
                        ['key' => 'social_pinterest', 'label' => 'Pinterest', 'placeholder' => 'https://pinterest.com/...', 'icon' => 'pinterest'],
                        ['key' => 'social_twitter',   'label' => 'X / Twitter', 'placeholder' => 'https://x.com/...', 'icon' => 'twitter'],
                    ] as $field)
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">{{ $field['label'] }}</label>
                        <input type="url" name="settings[{{ $field['key'] }}]"
                               value="{{ old('settings.'.$field['key'], $settings[$field['key']] ?? '') }}"
                               placeholder="{{ $field['placeholder'] }}"
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 border-t border-zinc-700 flex items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-xl font-bold text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar Ajustes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var tabs = document.querySelectorAll('.settings-tab');
    var panels = document.querySelectorAll('.settings-panel');
    var activeKey = 'settings-active-tab';

    function activateTab(id) {
        tabs.forEach(function (t) {
            var isActive = t.dataset.settingsTab === id;
            t.classList.toggle('text-amber-400', isActive);
            t.classList.toggle('border-amber-500', isActive);
            t.classList.toggle('text-zinc-400', !isActive);
            t.classList.toggle('border-transparent', !isActive);
        });
        panels.forEach(function (p) {
            p.classList.toggle('hidden', p.id !== 'settings-panel-' + id);
        });
        sessionStorage.setItem(activeKey, id);
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            activateTab(this.dataset.settingsTab);
        });
    });

    var stored = sessionStorage.getItem(activeKey) || 'general';
    activateTab(stored);
});
</script>
@endsection
