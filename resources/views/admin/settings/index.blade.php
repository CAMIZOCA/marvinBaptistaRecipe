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
                ['id' => 'ia',       'label' => '✦ IA'],
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

            {{-- ==================== IA ==================== --}}
            <div id="settings-panel-ia" class="settings-panel hidden space-y-5">

                {{-- Provider selector --}}
                @php $currentProvider = $settings['ai_provider'] ?? 'anthropic'; @endphp
                <div>
                    <p class="text-sm font-medium text-zinc-300 mb-3">Proveedor de IA</p>
                    <div class="grid md:grid-cols-2 gap-3">

                        <label class="ai-provider-card cursor-pointer" data-provider="anthropic">
                            <input type="radio" name="settings[ai_provider]" value="anthropic"
                                   {{ $currentProvider === 'anthropic' ? 'checked' : '' }} class="sr-only">
                            <div class="p-4 rounded-xl border-2 transition-all
                                {{ $currentProvider === 'anthropic' ? 'border-violet-500 bg-violet-900/20' : 'border-zinc-700 bg-zinc-700/30 hover:border-zinc-500' }}">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-7 h-7 bg-orange-500/20 rounded-lg flex items-center justify-center text-orange-400 text-xs font-bold">A</div>
                                    <span class="font-medium text-zinc-200 text-sm">Anthropic (Claude)</span>
                                </div>
                                <p class="text-xs text-zinc-500">API en la nube. Máxima calidad, requiere clave de pago. Ideal para producción.</p>
                            </div>
                        </label>

                        <label class="ai-provider-card cursor-pointer" data-provider="local">
                            <input type="radio" name="settings[ai_provider]" value="local"
                                   {{ $currentProvider === 'local' ? 'checked' : '' }} class="sr-only">
                            <div class="p-4 rounded-xl border-2 transition-all
                                {{ $currentProvider === 'local' ? 'border-violet-500 bg-violet-900/20' : 'border-zinc-700 bg-zinc-700/30 hover:border-zinc-500' }}">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-7 h-7 bg-emerald-500/20 rounded-lg flex items-center justify-center text-emerald-400 text-xs font-bold">⌘</div>
                                    <span class="font-medium text-zinc-200 text-sm">IA Local (localhost)</span>
                                </div>
                                <p class="text-xs text-zinc-500">Ollama, LM Studio, Jan u otro servidor OpenAI-compatible. Gratis y privado.</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- ── Anthropic section ── --}}
                <div id="ai-section-anthropic" class="{{ $currentProvider !== 'anthropic' ? 'hidden' : '' }} space-y-4 p-4 bg-zinc-700/20 rounded-xl border border-zinc-700">
                    <p class="text-xs font-semibold text-orange-400 uppercase tracking-wider">Configuración Anthropic</p>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">
                            Clave API
                            @if($settings['anthropic_api_key'] ?? null)
                            <span class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-900/40 border border-emerald-700/50 text-emerald-400 rounded-full text-xs font-normal">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Configurada
                            </span>
                            @endif
                        </label>
                        <div class="relative">
                            <input type="password" name="settings[anthropic_api_key]" id="anthropic_api_key" value=""
                                   placeholder="{{ $settings['anthropic_api_key'] ? 'sk-ant-••••••••••••••' : 'sk-ant-api03-...' }}"
                                   autocomplete="new-password"
                                   class="w-full px-4 py-2.5 pr-12 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                            <button type="button" id="toggle-key-visibility"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-300 transition-colors">
                                <svg class="w-4 h-4 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg class="w-4 h-4 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-zinc-500">
                            Deja vacío para conservar la clave actual. Consigue la tuya en
                            <a href="https://console.anthropic.com/" target="_blank" class="text-violet-400 hover:underline">console.anthropic.com</a>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Modelo</label>
                        <select name="settings[anthropic_model]"
                                class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 text-sm">
                            @foreach([
                                'claude-haiku-3-5'   => 'Claude Haiku 3.5 — Rápido y económico',
                                'claude-sonnet-4-5'  => 'Claude Sonnet 4.5 — Equilibrado',
                                'claude-sonnet-4-6'  => 'Claude Sonnet 4.6 — Recomendado ✦',
                                'claude-opus-4-5'    => 'Claude Opus 4.5 — Máxima calidad',
                            ] as $value => $label)
                            <option value="{{ $value }}"
                                {{ ($settings['anthropic_model'] ?? 'claude-sonnet-4-6') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- ── Local AI section ── --}}
                <div id="ai-section-local" class="{{ $currentProvider !== 'local' ? 'hidden' : '' }} space-y-4 p-4 bg-zinc-700/20 rounded-xl border border-zinc-700">
                    <p class="text-xs font-semibold text-emerald-400 uppercase tracking-wider">Configuración IA Local</p>

                    <div class="p-3 bg-zinc-900/50 rounded-xl border border-zinc-700 text-xs text-zinc-400 space-y-1">
                        <p class="font-medium text-zinc-300">URLs de ejemplo según servidor:</p>
                        <p><span class="text-emerald-400 font-mono">Ollama</span> → <code class="bg-zinc-800 px-1 rounded">http://localhost:11434</code></p>
                        <p><span class="text-blue-400 font-mono">LM Studio</span> → <code class="bg-zinc-800 px-1 rounded">http://localhost:1234</code></p>
                        <p><span class="text-amber-400 font-mono">Jan</span> → <code class="bg-zinc-800 px-1 rounded">http://localhost:1337</code></p>
                        <p class="text-zinc-500 pt-1">El endpoint <code class="bg-zinc-800 px-1 rounded">/v1/chat/completions</code> se agrega automáticamente.</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-zinc-300 mb-1.5">URL del servidor local</label>
                            <input type="url" name="settings[local_ai_url]"
                                   value="{{ old('settings.local_ai_url', $settings['local_ai_url'] ?? 'http://localhost:11434') }}"
                                   placeholder="http://localhost:11434"
                                   class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-1.5">Nombre del modelo</label>
                            <input type="text" name="settings[local_ai_model]"
                                   value="{{ old('settings.local_ai_model', $settings['local_ai_model'] ?? '') }}"
                                   placeholder="llama3.2 / mistral / phi4"
                                   class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <p class="mt-1 text-xs text-zinc-500">Tal como aparece en <code class="bg-zinc-800 px-1 rounded">ollama list</code></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-1.5">
                                API Key local
                                <span class="text-zinc-500 font-normal">(opcional)</span>
                            </label>
                            <input type="text" name="settings[local_ai_api_key]"
                                   value="{{ old('settings.local_ai_api_key', $settings['local_ai_api_key'] ?? '') }}"
                                   placeholder="local (o deja vacío)"
                                   class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <p class="mt-1 text-xs text-zinc-500">La mayoría de servidores locales no requieren clave.</p>
                        </div>
                    </div>
                </div>

                {{-- Test Connection --}}
                <div class="flex items-center gap-4 pt-1">
                    <button type="button" id="test-ai-btn"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 hover:bg-violet-500 text-white rounded-xl text-sm font-medium transition-colors disabled:opacity-50">
                        <svg id="test-ai-spinner" class="w-4 h-4 hidden animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <svg id="test-ai-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Probar conexión
                    </button>
                    <p class="text-xs text-zinc-500">Guarda primero, luego prueba la conexión.</p>
                </div>
                <div id="test-ai-result" class="hidden p-3 rounded-xl text-sm font-medium"></div>

                {{-- Feature summary --}}
                <div class="grid md:grid-cols-3 gap-3 pt-1">
                    @foreach([
                        ['label' => 'SEO Automático',    'desc' => 'Títulos y meta descriptions optimizados por receta'],
                        ['label' => 'Historia Cultural', 'desc' => 'Origen e historia de la receta para mayor retención'],
                        ['label' => 'FAQs + Tips',       'desc' => 'Preguntas frecuentes y secretos de chef para long-tail'],
                    ] as $card)
                    <div class="p-3 bg-zinc-700/30 border border-zinc-700 rounded-xl">
                        <p class="text-xs font-semibold text-violet-400 mb-1">{{ $card['label'] }}</p>
                        <p class="text-xs text-zinc-500">{{ $card['desc'] }}</p>
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

    // ── Provider toggle (Anthropic / Local) ───────────────────────
    var providerCards    = document.querySelectorAll('.ai-provider-card');
    var sectionAnthropic = document.getElementById('ai-section-anthropic');
    var sectionLocal     = document.getElementById('ai-section-local');

    function switchProvider(provider) {
        providerCards.forEach(function (card) {
            var isActive = card.dataset.provider === provider;
            var inner    = card.querySelector('div');
            inner.classList.toggle('border-violet-500', isActive);
            inner.classList.toggle('bg-violet-900/20',  isActive);
            inner.classList.toggle('border-zinc-700',   !isActive);
            inner.classList.toggle('bg-zinc-700/30',    !isActive);
        });
        if (sectionAnthropic) sectionAnthropic.classList.toggle('hidden', provider !== 'anthropic');
        if (sectionLocal)     sectionLocal.classList.toggle('hidden',     provider !== 'local');
    }

    providerCards.forEach(function (card) {
        card.addEventListener('click', function () {
            var radio = card.querySelector('input[type="radio"]');
            if (radio) { radio.checked = true; switchProvider(card.dataset.provider); }
        });
    });

    // ── Show/hide API key ──────────────────────────────────────────
    var toggleBtn = document.getElementById('toggle-key-visibility');
    var keyInput  = document.getElementById('anthropic_api_key');
    if (toggleBtn && keyInput) {
        toggleBtn.addEventListener('click', function () {
            var isPassword = keyInput.type === 'password';
            keyInput.type  = isPassword ? 'text' : 'password';
            toggleBtn.querySelector('.eye-open').classList.toggle('hidden', isPassword);
            toggleBtn.querySelector('.eye-closed').classList.toggle('hidden', !isPassword);
        });
    }

    // ── Test AI Connection ─────────────────────────────────────────
    var testBtn     = document.getElementById('test-ai-btn');
    var testResult  = document.getElementById('test-ai-result');
    var testSpinner = document.getElementById('test-ai-spinner');
    var testIcon    = document.getElementById('test-ai-icon');
    var csrfToken   = document.querySelector('meta[name="csrf-token"]')?.content
                   || '{{ csrf_token() }}';

    if (testBtn) {
        testBtn.addEventListener('click', function () {
            testBtn.disabled = true;
            testSpinner.classList.remove('hidden');
            testIcon.classList.add('hidden');
            testResult.classList.add('hidden');

            // Read current UI state (before saving)
            var selectedProvider = (document.querySelector('input[name="settings[ai_provider]"]:checked') || {}).value || 'anthropic';
            var payload = { provider: selectedProvider };
            if (selectedProvider === 'local') {
                var urlEl   = document.querySelector('input[name="settings[local_ai_url]"]');
                var modelEl = document.querySelector('input[name="settings[local_ai_model]"]');
                var keyEl   = document.querySelector('input[name="settings[local_ai_api_key]"]');
                if (urlEl)   payload.local_ai_url       = urlEl.value;
                if (modelEl) payload.local_ai_model     = modelEl.value;
                if (keyEl)   payload.local_ai_api_key   = keyEl.value;
            }

            fetch('{{ route('admin.settings.test-ai') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                testResult.classList.remove('hidden', 'bg-emerald-900/40', 'border-emerald-700/50', 'text-emerald-300',
                                                        'bg-red-900/40',     'border-red-700/50',     'text-red-300');
                if (data.ok) {
                    testResult.className = 'mt-3 p-3 rounded-xl text-sm font-medium bg-emerald-900/40 border border-emerald-700/50 text-emerald-300';
                    testResult.textContent = '✓ ' + data.message;
                } else {
                    testResult.className = 'mt-3 p-3 rounded-xl text-sm font-medium bg-red-900/40 border border-red-700/50 text-red-300';
                    testResult.textContent = '✗ ' + data.message;
                }
            })
            .catch(function () {
                testResult.className = 'mt-3 p-3 rounded-xl text-sm font-medium bg-red-900/40 border border-red-700/50 text-red-300';
                testResult.textContent = '✗ Error de red. Verifica tu conexión.';
                testResult.classList.remove('hidden');
            })
            .finally(function () {
                testBtn.disabled = false;
                testSpinner.classList.add('hidden');
                testIcon.classList.remove('hidden');
            });
        });
    }
});
</script>
@endsection
