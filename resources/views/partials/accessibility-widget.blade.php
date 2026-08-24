<!-- Floating Accessibility Widget -->
<div x-data="{ open: false, contrast: false, tts: false, fontSize: 100 }" class="fixed bottom-6 left-6 z-50">
    <!-- Trigger Button -->
    <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-label="Buka menu aksesibilitas disabilitas" class="h-12 w-12 rounded-full bg-teal-800 hover:bg-teal-900 text-white shadow-md flex items-center justify-center text-xl focus-visible:ring-2 focus-visible:ring-teal-700 focus-visible:ring-offset-2">
        <span aria-hidden="true">♿</span>
    </button>

    <!-- Menu Options -->
    <div x-show="open" @click.outside="open = false" x-transition role="dialog" aria-label="Pengaturan Aksesibilitas" class="absolute bottom-16 left-0 bg-white border border-slate-200 rounded-xl p-4 shadow-xl w-64 space-y-3.5 text-slate-800">
        <h3 class="font-outfit font-bold text-slate-900 text-sm border-b border-slate-200 pb-2">
            {{ app()->getLocale() == 'en' ? 'Accessibility Settings' : 'Pengaturan Aksesibilitas' }}
        </h3>
        
        <!-- Font Size Controls -->
        <div class="space-y-1.5">
            <span class="text-xs text-slate-600 font-semibold block">{{ app()->getLocale() == 'en' ? 'Text Size' : 'Ukuran Teks' }}</span>
            <div class="flex gap-2">
                <button type="button" @click="fontSize = Math.max(80, fontSize - 10); document.documentElement.style.fontSize = fontSize + '%';" aria-label="Perkecil teks" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-800 py-1.5 rounded text-xs font-bold transition min-h-[36px]">A-</button>
                <button type="button" @click="fontSize = 100; document.documentElement.style.fontSize = '100%';" aria-label="Ukuran teks normal" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-800 py-1.5 rounded text-xs font-bold transition min-h-[36px]">Normal</button>
                <button type="button" @click="fontSize = Math.min(150, fontSize + 10); document.documentElement.style.fontSize = fontSize + '%';" aria-label="Perbesar teks" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-800 py-1.5 rounded text-xs font-bold transition min-h-[36px]">A+</button>
            </div>
        </div>

        <!-- High Contrast Mode Toggle -->
        <div class="flex items-center justify-between border-t border-slate-200 pt-3">
            <span class="text-xs text-slate-700 font-semibold">{{ app()->getLocale() == 'en' ? 'High Contrast' : 'Kontras Tinggi' }}</span>
            <button type="button" @click="contrast = !contrast; document.documentElement.classList.toggle('accessibility-contrast');" aria-label="Aktifkan mode kontras tinggi" class="px-3 py-1 rounded text-xs font-bold transition min-h-[32px]" :class="contrast ? 'bg-teal-800 text-white' : 'bg-slate-200 text-slate-800 hover:bg-slate-300'">
                <span x-text="contrast ? 'AKTIF' : 'NONAKTIF'"></span>
            </button>
        </div>

        <!-- Text-to-Speech Screen Reader Toggle -->
        <div class="flex items-center justify-between border-t border-slate-200 pt-3">
            <div>
                <span class="text-xs text-slate-700 font-semibold block">{{ app()->getLocale() == 'en' ? 'Screen Reader (TTS)' : 'Pembaca Suara (TTS)' }}</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">{{ app()->getLocale() == 'en' ? 'Hover text to speak' : 'Arahkan kursor ke teks' }}</span>
            </div>
            <button type="button" @click="tts = !tts; if(tts) { enableTTS(); } else { disableTTS(); }" aria-label="Aktifkan pembaca suara otomatis" class="px-3 py-1 rounded text-xs font-bold transition min-h-[32px]" :class="tts ? 'bg-teal-800 text-white' : 'bg-slate-200 text-slate-800 hover:bg-slate-300'">
                <span x-text="tts ? 'AKTIF' : 'NONAKTIF'"></span>
            </button>
        </div>
    </div>
</div>

<!-- Custom High Contrast CSS & Text-to-Speech JS Script -->
<style>
    .accessibility-contrast {
        filter: contrast(120%) !important;
    }
    .accessibility-contrast body {
        background-color: #020617 !important;
        color: #f8fafc !important;
    }
    .accessibility-contrast .bg-white, 
    .accessibility-contrast .content-card, 
    .accessibility-contrast .hero-card {
        background-color: #0f172a !important;
        border-color: #fde047 !important;
        color: #f8fafc !important;
    }
    .accessibility-contrast text, 
    .accessibility-contrast p, 
    .accessibility-contrast h1, 
    .accessibility-contrast h2, 
    .accessibility-contrast h3,
    .accessibility-contrast span {
        color: #f8fafc !important;
    }
    .accessibility-contrast .text-teal-700, 
    .accessibility-contrast .text-teal-800 {
        color: #fde047 !important;
    }
</style>
<script>
    let ttsActive = false;
    let currentUtterance = null;

    function speakText(e) {
        if (!ttsActive) return;
        window.speechSynthesis.cancel();
        
        let text = e.target.innerText || e.target.textContent;
        if (!text || text.trim().length === 0) return;
        
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA') {
            text = "Kolom isian untuk " + (e.target.placeholder || e.target.name);
        }

        currentUtterance = new SpeechSynthesisUtterance(text.trim());
        currentUtterance.lang = "{{ app()->getLocale() == 'en' ? 'en-US' : 'id-ID' }}";
        window.speechSynthesis.speak(currentUtterance);
    }

    function stopSpeaking() {
        if (ttsActive) {
            window.speechSynthesis.cancel();
        }
    }

    function enableTTS() {
        ttsActive = true;
        document.querySelectorAll('h1, h2, h3, h4, p, label, button, a, strong, span').forEach(el => {
            el.addEventListener('mouseenter', speakText);
            el.addEventListener('mouseleave', stopSpeaking);
        });
    }

    function disableTTS() {
        ttsActive = false;
        window.speechSynthesis.cancel();
        document.querySelectorAll('h1, h2, h3, h4, p, label, button, a, strong, span').forEach(el => {
            el.removeEventListener('mouseenter', speakText);
            el.removeEventListener('mouseleave', stopSpeaking);
        });
    }
</script>
