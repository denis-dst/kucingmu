<x-app-layout>
    <div class="py-8 sm:py-10" x-data="{ activeTab: 'analytics' }">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Header Panel -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="eyebrow">Pengaturan Sistem &amp; Integrasi</span>
                    <h1 class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
                        Settings Apps KucingMu
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-2xl mt-1 leading-relaxed">
                        Kelola konfigurasi global aplikasi, integrasi pelacakan Google Analytics &amp; Google Tag Manager, branding visual, serta setelan operasional sistem.
                    </p>
                </div>
                <div class="hidden sm:flex w-14 h-14 rounded-2xl bg-teal-50 text-teal-800 border border-teal-200/80 items-center justify-center text-2xl shadow-inner">
                    ⚙️
                </div>
            </div>

            <!-- Success Flash Alert -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs sm:text-sm font-semibold flex items-center justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-2">
                        <span>✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Navigation Tabs -->
            <div class="flex flex-wrap items-center gap-2 p-1.5 bg-slate-200/80 rounded-2xl border border-slate-200/80 text-xs font-semibold">
                <button type="button" 
                        @click="activeTab = 'analytics'"
                        :class="activeTab === 'analytics' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-4 py-2 rounded-xl transition flex items-center gap-2">
                    <span>📊</span>
                    <span>Google Analytics &amp; GTM</span>
                    @if(!empty($settings['google_analytics_id']->value ?? null) || !empty($settings['google_tag_manager_id']->value ?? null) || !empty($settings['google_analytics_script']->value ?? null) || !empty($settings['google_tag_manager_head']->value ?? null))
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    @endif
                </button>

                <button type="button" 
                        @click="activeTab = 'general'"
                        :class="activeTab === 'general' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-4 py-2 rounded-xl transition flex items-center gap-2">
                    <span>🏛️</span>
                    <span>Umum &amp; Kontak</span>
                </button>

                <button type="button" 
                        @click="activeTab = 'branding'"
                        :class="activeTab === 'branding' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-4 py-2 rounded-xl transition flex items-center gap-2">
                    <span>🎨</span>
                    <span>Branding &amp; Logo</span>
                </button>

                <button type="button" 
                        @click="activeTab = 'features'"
                        :class="activeTab === 'features' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-4 py-2 rounded-xl transition flex items-center gap-2">
                    <span>🔒</span>
                    <span>Fitur &amp; Operasional</span>
                </button>
            </div>

            <!-- Settings Form Card -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-sm">
                <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- TAB 1: GOOGLE ANALYTICS & GOOGLE TAG MANAGER -->
                    <div x-show="activeTab === 'analytics'" class="space-y-8" x-cloak>
                        
                        <div class="pb-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h2 class="font-outfit font-bold text-lg text-slate-900 flex items-center gap-2">
                                    <span>📊</span> Integrasi Google Analytics &amp; Google Tag Manager
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Tempelkan script tag atau masukkan ID pelacakan untuk memantau trafik pengunjung dan interaksi web secara otomatis.
                                </p>
                            </div>
                        </div>

                        <!-- Guide Banner -->
                        <div class="p-4 bg-teal-50/70 border border-teal-200 rounded-2xl space-y-2 text-xs text-teal-950">
                            <div class="font-bold flex items-center gap-1.5 text-teal-900">
                                <span>💡</span> Panduan Pengisian Skrip Pelacakan
                            </div>
                            <p class="text-teal-800 leading-relaxed">
                                Anda dapat mengisi <strong>ID Kontainer / ID Pengukuran saja</strong>, atau langsung menempelkan (<em>copy-paste</em>) seluruh blok kode <code>&lt;script&gt;...&lt;/script&gt;</code> yang diberikan oleh Google. Skrip akan disisipkan secara otomatis ke seluruh halaman web publik dan aplikasi.
                            </p>
                        </div>

                        <!-- Section: Google Tag Manager (GTM) -->
                        <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl border border-slate-200 space-y-5">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-xl">🏷️</span>
                                    <div>
                                        <h3 class="font-outfit font-bold text-base text-slate-900">Google Tag Manager (GTM)</h3>
                                        <p class="text-[11px] text-slate-500">Pengelolaan tag terpusat untuk analitik, event conversion, dan integrasi marketing.</p>
                                    </div>
                                </div>
                                <div>
                                    @if(!empty($settings['google_tag_manager_id']->value ?? null) || !empty($settings['google_tag_manager_head']->value ?? null))
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Terpasang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-medium bg-slate-200 text-slate-600">
                                            Belum Aktif
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- GTM ID -->
                            <div class="space-y-1.5">
                                <label for="setting_google_tag_manager_id" class="block font-outfit text-xs font-bold text-slate-800">
                                    GTM Container ID (Opsional jika menempelkan script tag di bawah)
                                </label>
                                <input type="text" 
                                       id="setting_google_tag_manager_id" 
                                       name="settings[google_tag_manager_id]" 
                                       value="{{ old('settings.google_tag_manager_id', $settings['google_tag_manager_id']->value ?? '') }}" 
                                       placeholder="Contoh: GTM-XXXXXXX"
                                       class="form-input text-xs font-mono py-2 rounded-xl">
                                <span class="text-[11px] text-slate-400 block">
                                    Jika Anda hanya mengisi ID ini, template standar GTM resmi Google akan otomatis dimuat.
                                </span>
                            </div>

                            <!-- GTM Head Script -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label for="setting_google_tag_manager_head" class="block font-outfit text-xs font-bold text-slate-800">
                                        Skrip Tag GTM untuk Header (&lt;head&gt;)
                                    </label>
                                    <span class="text-[10px] font-mono text-slate-500">Ditempatkan di bagian atas &lt;head&gt;</span>
                                </div>
                                <div class="rounded-2xl border border-slate-700 bg-slate-950 overflow-hidden shadow-sm focus-within:ring-2 focus-within:ring-teal-400 focus-within:border-teal-400">
                                    <div class="flex items-center justify-between px-4 py-2 bg-slate-900 border-b border-slate-800 text-[11px] font-mono">
                                        <span class="flex items-center gap-1.5 font-bold text-teal-300">
                                            <span>&lt;/&gt;</span> GTM Header Script Tag
                                        </span>
                                        <span class="text-[10px] text-slate-400">Paste cuplikan &lt;script&gt; GTM</span>
                                    </div>
                                    <textarea id="setting_google_tag_manager_head" 
                                              name="settings[google_tag_manager_head]" 
                                              rows="5" 
                                              spellcheck="false"
                                              autocomplete="off"
                                              autocorrect="off"
                                              autocapitalize="off"
                                              placeholder="<!-- Google Tag Manager -->&#10;<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':&#10;new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],&#10;j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=&#10;'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);&#10;})(window,document,'script','dataLayer','GTM-XXXXXXX');</script>&#10;<!-- End Google Tag Manager -->"
                                              style="background-color: #020617 !important; color: #38bdf8 !important; caret-color: #38bdf8 !important; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace !important; line-height: 1.6 !important;"
                                              class="w-full p-4 font-mono text-xs border-0 outline-none focus:outline-none focus:ring-0 placeholder:text-slate-600 block">{{ old('settings.google_tag_manager_head', $settings['google_tag_manager_head']->value ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- GTM Body Script -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label for="setting_google_tag_manager_body" class="block font-outfit text-xs font-bold text-slate-800">
                                        Skrip Tag GTM untuk Body (&lt;noscript&gt;)
                                    </label>
                                    <span class="text-[10px] font-mono text-slate-500">Ditempatkan tepat setelah pembuka &lt;body&gt;</span>
                                </div>
                                <div class="rounded-2xl border border-slate-700 bg-slate-950 overflow-hidden shadow-sm focus-within:ring-2 focus-within:ring-teal-400 focus-within:border-teal-400">
                                    <div class="flex items-center justify-between px-4 py-2 bg-slate-900 border-b border-slate-800 text-[11px] font-mono">
                                        <span class="flex items-center gap-1.5 font-bold text-teal-300">
                                            <span>&lt;/&gt;</span> GTM Body NoScript Tag
                                        </span>
                                        <span class="text-[10px] text-slate-400">Paste cuplikan &lt;noscript&gt; GTM</span>
                                    </div>
                                    <textarea id="setting_google_tag_manager_body" 
                                              name="settings[google_tag_manager_body]" 
                                              rows="4" 
                                              spellcheck="false"
                                              autocomplete="off"
                                              autocorrect="off"
                                              autocapitalize="off"
                                              placeholder="<!-- Google Tag Manager (noscript) -->&#10;<noscript><iframe src=&quot;https://www.googletagmanager.com/ns.html?id=GTM-XXXXXXX&quot;&#10;height=&quot;0&quot; width=&quot;0&quot; style=&quot;display:none;visibility:hidden&quot;></iframe></noscript>&#10;<!-- End Google Tag Manager (noscript) -->"
                                              style="background-color: #020617 !important; color: #38bdf8 !important; caret-color: #38bdf8 !important; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace !important; line-height: 1.6 !important;"
                                              class="w-full p-4 font-mono text-xs border-0 outline-none focus:outline-none focus:ring-0 placeholder:text-slate-600 block">{{ old('settings.google_tag_manager_body', $settings['google_tag_manager_body']->value ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Google Analytics 4 (GA4) -->
                        <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl border border-slate-200 space-y-5">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-xl">📈</span>
                                    <div>
                                        <h3 class="font-outfit font-bold text-base text-slate-900">Google Analytics 4 (GA4 / gtag.js)</h3>
                                        <p class="text-[11px] text-slate-500">Statistik pengunjung, halaman terpopuler, demografi, dan konversi pendaftaran.</p>
                                    </div>
                                </div>
                                <div>
                                    @if(!empty($settings['google_analytics_id']->value ?? null) || !empty($settings['google_analytics_script']->value ?? null))
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Terpasang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-medium bg-slate-200 text-slate-600">
                                            Belum Aktif
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- GA4 ID -->
                            <div class="space-y-1.5">
                                <label for="setting_google_analytics_id" class="block font-outfit text-xs font-bold text-slate-800">
                                    GA4 Measurement ID (ID Pengukuran)
                                </label>
                                <input type="text" 
                                       id="setting_google_analytics_id" 
                                       name="settings[google_analytics_id]" 
                                       value="{{ old('settings.google_analytics_id', $settings['google_analytics_id']->value ?? '') }}" 
                                       placeholder="Contoh: G-XXXXXXXXXX"
                                       class="form-input text-xs font-mono py-2 rounded-xl">
                                <span class="text-[11px] text-slate-400 block">
                                    Cukup isi ID ini (awalan G-) untuk memuat skrip resmi Google Analytics otomatis.
                                </span>
                            </div>

                            <!-- GA4 Custom Tag Script -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label for="setting_google_analytics_script" class="block font-outfit text-xs font-bold text-slate-800">
                                        Atau Tempelkan Skrip Google Tag (gtag.js) Lengkap
                                    </label>
                                    <span class="text-[10px] font-mono text-slate-500">Opsional jika sudah mengisi ID di atas</span>
                                </div>
                                <div class="rounded-2xl border border-slate-700 bg-slate-950 overflow-hidden shadow-sm focus-within:ring-2 focus-within:ring-teal-400 focus-within:border-teal-400">
                                    <div class="flex items-center justify-between px-4 py-2 bg-slate-900 border-b border-slate-800 text-[11px] font-mono">
                                        <span class="flex items-center gap-1.5 font-bold text-emerald-300">
                                            <span>&lt;/&gt;</span> GA4 gtag.js Script Tag
                                        </span>
                                        <span class="text-[10px] text-slate-400">Paste cuplikan &lt;script&gt; gtag.js</span>
                                    </div>
                                    <textarea id="setting_google_analytics_script" 
                                              name="settings[google_analytics_script]" 
                                              rows="5" 
                                              spellcheck="false"
                                              autocomplete="off"
                                              autocorrect="off"
                                              autocapitalize="off"
                                              placeholder="<!-- Google tag (gtag.js) -->&#10;<script async src=&quot;https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX&quot;></script>&#10;<script>&#10;  window.dataLayer = window.dataLayer || [];&#10;  function gtag(){dataLayer.push(arguments);}&#10;  gtag('js', new Date());&#10;  gtag('config', 'G-XXXXXXXXXX');&#10;</script>"
                                              style="background-color: #020617 !important; color: #34d399 !important; caret-color: #34d399 !important; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace !important; line-height: 1.6 !important;"
                                              class="w-full p-4 font-mono text-xs border-0 outline-none focus:outline-none focus:ring-0 placeholder:text-slate-600 block">{{ old('settings.google_analytics_script', $settings['google_analytics_script']->value ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Skrip Kustom Tambahan -->
                        <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl border border-slate-200 space-y-4">
                            <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200">
                                <span class="text-xl">🛠️</span>
                                <div>
                                    <h3 class="font-outfit font-bold text-base text-slate-900">Skrip Pelacakan Tambahan (Opsional)</h3>
                                    <p class="text-[11px] text-slate-500">Gunakan untuk Meta Pixel, TikTok Pixel, Webmaster Verification, atau widget chat pihak ketiga.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="setting_custom_head_scripts" class="block font-outfit text-xs font-bold text-slate-800">
                                        Skrip Tambahan Header (&lt;head&gt;)
                                    </label>
                                    <div class="rounded-2xl border border-slate-700 bg-slate-950 overflow-hidden shadow-sm focus-within:ring-2 focus-within:ring-teal-400 focus-within:border-teal-400">
                                        <div class="flex items-center justify-between px-3.5 py-1.5 bg-slate-900 border-b border-slate-800 text-[11px] font-mono text-slate-300">
                                            <span class="text-teal-300 font-bold">&lt;head&gt; Custom</span>
                                        </div>
                                        <textarea id="setting_custom_head_scripts" 
                                                  name="settings[custom_head_scripts]" 
                                                  rows="4" 
                                                  spellcheck="false"
                                                  autocomplete="off"
                                                  autocorrect="off"
                                                  autocapitalize="off"
                                                  placeholder="<!-- Meta Pixel Code, Verification Tag, etc. -->"
                                                  style="background-color: #020617 !important; color: #38bdf8 !important; caret-color: #38bdf8 !important; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important; line-height: 1.6 !important;"
                                                  class="w-full p-3 font-mono text-xs border-0 outline-none focus:outline-none focus:ring-0 placeholder:text-slate-600 block">{{ old('settings.custom_head_scripts', $settings['custom_head_scripts']->value ?? '') }}</textarea>
                                    </div>
                                </div>

                                <div class="space-y-1.5">
                                    <label for="setting_custom_body_scripts" class="block font-outfit text-xs font-bold text-slate-800">
                                        Skrip Tambahan Body (sebelum &lt;/body&gt;)
                                    </label>
                                    <div class="rounded-2xl border border-slate-700 bg-slate-950 overflow-hidden shadow-sm focus-within:ring-2 focus-within:ring-teal-400 focus-within:border-teal-400">
                                        <div class="flex items-center justify-between px-3.5 py-1.5 bg-slate-900 border-b border-slate-800 text-[11px] font-mono text-slate-300">
                                            <span class="text-teal-300 font-bold">&lt;/body&gt; Custom</span>
                                        </div>
                                        <textarea id="setting_custom_body_scripts" 
                                                  name="settings[custom_body_scripts]" 
                                                  rows="4" 
                                                  spellcheck="false"
                                                  autocomplete="off"
                                                  autocorrect="off"
                                                  autocapitalize="off"
                                                  placeholder="<!-- Chat Widget, Bottom tracking scripts, etc. -->"
                                                  style="background-color: #020617 !important; color: #38bdf8 !important; caret-color: #38bdf8 !important; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important; line-height: 1.6 !important;"
                                                  class="w-full p-3 font-mono text-xs border-0 outline-none focus:outline-none focus:ring-0 placeholder:text-slate-600 block">{{ old('settings.custom_body_scripts', $settings['custom_body_scripts']->value ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- TAB 2: UMUM & KONTAK -->
                    <div x-show="activeTab === 'general'" class="space-y-6" x-cloak>
                        <div class="pb-3 border-b border-slate-100">
                            <h2 class="font-outfit font-bold text-lg text-slate-900 flex items-center gap-2">
                                <span>🏛️</span> Informasi Umum &amp; Kontak Resmi
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Pengaturan identitas dasar platform, narasi deskripsi, dan kontak pusat.</p>
                        </div>

                        <div class="space-y-4">
                            <!-- App Name -->
                            <div class="space-y-1">
                                <label for="setting_app_name" class="block font-outfit text-xs font-bold text-slate-800">Nama Platform / Aplikasi</label>
                                <input type="text" id="setting_app_name" name="settings[app_name]" value="{{ old('settings.app_name', $settings['app_name']->value ?? 'KucingMu') }}" class="form-input text-xs py-2 rounded-xl" required>
                            </div>

                            <!-- App Description -->
                            <div class="space-y-1">
                                <label for="setting_app_description" class="block font-outfit text-xs font-bold text-slate-800">Deskripsi Singkat (SEO Meta Description)</label>
                                <textarea id="setting_app_description" name="settings[app_description]" rows="3" class="form-input text-xs rounded-xl">{{ old('settings.app_description', $settings['app_description']->value ?? '') }}</textarea>
                            </div>

                            <!-- Contact Email -->
                            <div class="space-y-1">
                                <label for="setting_contact_email" class="block font-outfit text-xs font-bold text-slate-800">Email Kontak Resmi</label>
                                <input type="text" id="setting_contact_email" name="settings[contact_email]" value="{{ old('settings.contact_email', $settings['contact_email']->value ?? '') }}" class="form-input text-xs py-2 rounded-xl">
                            </div>

                            <!-- Office Address -->
                            <div class="space-y-1">
                                <label for="setting_office_address" class="block font-outfit text-xs font-bold text-slate-800">Alamat Kantor / Sekretariat</label>
                                <textarea id="setting_office_address" name="settings[office_address]" rows="2" class="form-input text-xs rounded-xl">{{ old('settings.office_address', $settings['office_address']->value ?? '') }}</textarea>
                            </div>

                            <!-- App Footer -->
                            <div class="space-y-1">
                                <label for="setting_app_footer" class="block font-outfit text-xs font-bold text-slate-800">Teks Hak Cipta Footer</label>
                                <input type="text" id="setting_app_footer" name="settings[app_footer]" value="{{ old('settings.app_footer', $settings['app_footer']->value ?? '') }}" class="form-input text-xs py-2 rounded-xl">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: BRANDING & LOGO -->
                    <div x-show="activeTab === 'branding'" class="space-y-6" x-cloak>
                        <div class="pb-3 border-b border-slate-100">
                            <h2 class="font-outfit font-bold text-lg text-slate-900 flex items-center gap-2">
                                <span>🎨</span> Branding Visual &amp; Logo
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Unggah berkas logo utama, favicon browser, dan logo kartu identitas kucing.</p>
                        </div>

                        <div class="space-y-6">
                            <!-- App Logo -->
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs text-slate-800">Logo Utama Aplikasi (Navbar &amp; Auth)</span>
                                    <span class="text-[10px] text-slate-400">PNG / WebP Transparan</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    @if(!empty($settings['app_logo']->value ?? null))
                                        <div class="h-16 w-16 bg-white rounded-xl p-1.5 border border-slate-200 flex items-center justify-center shadow-xs">
                                            <img src="{{ asset('storage/' . $settings['app_logo']->value) }}" alt="Logo KucingMu" class="max-h-full max-w-full object-contain">
                                        </div>
                                    @else
                                        <div class="h-16 w-16 bg-slate-200 rounded-xl flex items-center justify-center text-xs text-slate-400 font-bold">
                                            Default
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <input type="file" name="settings[app_logo]" accept="image/*" class="form-input text-xs py-1.5 rounded-xl">
                                    </div>
                                </div>
                            </div>

                            <!-- App Favicon -->
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs text-slate-800">Favicon Browser</span>
                                    <span class="text-[10px] text-slate-400">Icon tab browser (.ico, .png)</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    @if(!empty($settings['app_favicon']->value ?? null))
                                        <div class="h-12 w-12 bg-white rounded-xl p-1 border border-slate-200 flex items-center justify-center shadow-xs">
                                            <img src="{{ asset('storage/' . $settings['app_favicon']->value) }}" alt="Favicon" class="max-h-full max-w-full object-contain">
                                        </div>
                                    @else
                                        <div class="h-12 w-12 bg-slate-200 rounded-xl flex items-center justify-center text-xs text-slate-400 font-bold">
                                            Default
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <input type="file" name="settings[app_favicon]" accept="image/*" class="form-input text-xs py-1.5 rounded-xl">
                                    </div>
                                </div>
                            </div>

                            <!-- KTAM Logo -->
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs text-slate-800">Logo Khusus Kartu Tanda Anggota (KTAKuMu)</span>
                                    <span class="text-[10px] text-slate-400">Dicetak pada PDF/Fisik KTAM</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    @if(!empty($settings['ktam_logo']->value ?? null))
                                        <div class="h-16 w-16 bg-white rounded-xl p-1.5 border border-slate-200 flex items-center justify-center shadow-xs">
                                            <img src="{{ asset('storage/' . $settings['ktam_logo']->value) }}" alt="Logo KTAM" class="max-h-full max-w-full object-contain">
                                        </div>
                                    @else
                                        <div class="h-16 w-16 bg-slate-200 rounded-xl flex items-center justify-center text-xs text-slate-400 font-bold">
                                            Default
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <input type="file" name="settings[ktam_logo]" accept="image/*" class="form-input text-xs py-1.5 rounded-xl">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: FITUR & OPERASIONAL -->
                    <div x-show="activeTab === 'features'" class="space-y-6" x-cloak>
                        <div class="pb-3 border-b border-slate-100">
                            <h2 class="font-outfit font-bold text-lg text-slate-900 flex items-center gap-2">
                                <span>🔒</span> Fitur &amp; Mode Operasional
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Kendali ketersediaan modul pendaftaran, janji temu medis, dan mode perawatan sistem.</p>
                        </div>

                        <div class="space-y-4">
                            <!-- Allow Registrations -->
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <span class="font-bold text-xs text-slate-800 block">Pendaftaran Kucing Baru (Publik)</span>
                                    <span class="text-[11px] text-slate-500">Mengizinkan anggota komunitas mendaftarkan kucing peliharaan secara mandiri.</span>
                                </div>
                                <select name="settings[allow_registrations]" class="form-input text-xs py-2 rounded-xl w-full sm:w-44">
                                    <option value="1" {{ ($settings['allow_registrations']->value ?? '1') == '1' ? 'selected' : '' }}>🟢 Aktif (Buka)</option>
                                    <option value="0" {{ ($settings['allow_registrations']->value ?? '1') == '0' ? 'selected' : '' }}>🔴 Nonaktif (Tutup)</option>
                                </select>
                            </div>

                            <!-- Enable Appointments -->
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <span class="font-bold text-xs text-slate-800 block">Modul Jadwal &amp; Janji Temu Dokter Hewan</span>
                                    <span class="text-[11px] text-slate-500">Fitur reservasi pemeriksaan kesehatan dan vaksinasi dengan dokter hewan mitra.</span>
                                </div>
                                <select name="settings[enable_appointments]" class="form-input text-xs py-2 rounded-xl w-full sm:w-44">
                                    <option value="1" {{ ($settings['enable_appointments']->value ?? '1') == '1' ? 'selected' : '' }}>🟢 Aktif (Tampil)</option>
                                    <option value="0" {{ ($settings['enable_appointments']->value ?? '1') == '0' ? 'selected' : '' }}>🔴 Nonaktif (Sembunyi)</option>
                                </select>
                            </div>

                            <!-- Maintenance Mode -->
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <span class="font-bold text-xs text-slate-800 block">Mode Pemeliharaan (Maintenance Mode)</span>
                                    <span class="text-[11px] text-slate-500">Menampilkan pengumuman pemeliharaan bagi pengguna non-administrator.</span>
                                </div>
                                <select name="settings[maintenance_mode]" class="form-input text-xs py-2 rounded-xl w-full sm:w-44">
                                    <option value="0" {{ ($settings['maintenance_mode']->value ?? '0') == '0' ? 'selected' : '' }}>🟢 Normal (Operasional)</option>
                                    <option value="1" {{ ($settings['maintenance_mode']->value ?? '0') == '1' ? 'selected' : '' }}>🟠 Mode Pemeliharaan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons Bar -->
                    <div class="flex items-center justify-between pt-6 border-t border-slate-100 gap-3">
                        <a href="{{ route('dashboard') }}" class="button-secondary text-xs px-5 py-2.5 rounded-xl font-semibold">
                            ← Kembali ke Dashboard
                        </a>
                        <button type="submit" class="button-primary text-xs px-6 py-2.5 rounded-xl font-bold bg-teal-700 hover:bg-teal-800 text-white shadow-xs">
                            💾 Simpan Semua Pengaturan
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
