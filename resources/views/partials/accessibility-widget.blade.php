<!-- Floating Accessibility Widget -->
<div x-data="{ open: false, contrast: false, tts: false, fontSize: 100 }" class="fixed bottom-6 left-6 z-50">
    <!-- Trigger Button -->
    <button @click="open = !open" class="h-12 w-12 rounded-full bg-teal-800 hover:bg-teal-900 text-white shadow-lg flex items-center justify-center text-xl transition transform hover:scale-105 focus:outline-none" title="Widget Aksesibilitas">
        ♿
    </button>

    <!-- Menu Options -->
    <div x-show="open" @click.outside="open = false" x-transition class="absolute bottom-16 left-0 bg-white border border-slate-200 rounded-2xl p-4 shadow-2xl w-64 space-y-4">
        <h3 class="font-outfit font-bold text-slate-800 text-sm border-b border-slate-100 pb-2 flex items-center gap-1.5">
            <span>♿</span> {{ app()->getLocale() == 'en' ? 'Accessibility Options' : 'Aksesibilitas Disabilitas' }}
        </h3>
        
        <!-- Font Size Controls -->
        <div class="space-y-1.5">
            <span class="text-xs text-slate-500 font-semibold block">{{ app()->getLocale() == 'en' ? 'Text Size' : 'Ukuran Teks' }}</span>
            <div class="flex gap-2">
                <button @click="fontSize = Math.max(80, fontSize - 10); document.documentElement.style.fontSize = fontSize + '%';" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-1.5 rounded-lg text-xs font-bold transition">A-</button>
                <button @click="fontSize = 100; document.documentElement.style.fontSize = '100%';" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-1.5 rounded-lg text-xs font-bold transition">Normal</button>
                <button @click="fontSize = Math.min(150, fontSize + 10); document.documentElement.style.fontSize = fontSize + '%';" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-1.5 rounded-lg text-xs font-bold transition">A+</button>
            </div>
        </div>

        <!-- High Contrast Mode Toggle -->
        <div class="flex items-center justify-between border-t border-slate-50 pt-3">
            <span class="text-xs text-slate-500 font-semibold">{{ app()->getLocale() == 'en' ? 'High Contrast' : 'Kontras Tinggi' }}</span>
            <button @click="contrast = !contrast; document.documentElement.classList.toggle('accessibility-contrast');" class="px-3 py-1 rounded-full text-xs font-bold transition" :class="contrast ? 'bg-teal-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'">
                <span x-text="contrast ? 'ON' : 'OFF'"></span>
            </button>
        </div>

        <!-- Text-to-Speech Screen Reader Toggle -->
        <div class="flex items-center justify-between border-t border-slate-50 pt-3">
            <div>
                <span class="text-xs text-slate-500 font-semibold block">{{ app()->getLocale() == 'en' ? 'Screen Reader (TTS)' : 'Pembaca Suara (TTS)' }}</span>
                <span class="text-[9px] text-slate-400 block mt-0.5">{{ app()->getLocale() == 'en' ? 'Hover text to read aloud' : 'Arahkan kursor ke teks untuk membaca' }}</span>
            </div>
            <button @click="tts = !tts; if(tts) { enableTTS(); } else { disableTTS(); }" class="px-3 py-1 rounded-full text-xs font-bold transition" :class="tts ? 'bg-teal-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'">
                <span x-text="tts ? 'ON' : 'OFF'"></span>
            </button>
        </div>
    </div>
</div>

<!-- Custom High Contrast CSS & Text-to-Speech JS Script -->
<style>
    .accessibility-contrast {
        filter: contrast(130%) grayscale(60%) !important;
    }
    .accessibility-contrast body {
        background-color: #000000 !important;
        color: #ffffff !important;
    }
    .accessibility-contrast .bg-white, 
    .accessibility-contrast .content-card, 
    .accessibility-contrast .hero-card {
        background-color: #121212 !important;
        border-color: #ffd700 !important;
        color: #ffffff !important;
    }
    .accessibility-contrast text, 
    .accessibility-contrast p, 
    .accessibility-contrast h1, 
    .accessibility-contrast h2, 
    .accessibility-contrast h3,
    .accessibility-contrast span {
        color: #ffffff !important;
    }
    .accessibility-contrast .text-teal-700, 
    .accessibility-contrast .text-teal-800 {
        color: #ffd700 !important;
    }
</style>
<script>
    let ttsActive = false;
    let currentUtterance = null;

    function speakText(e) {
        if (!ttsActive) return;
        
        // Stop any currently speaking voice
        window.speechSynthesis.cancel();
        
        let text = e.target.innerText || e.target.textContent;
        if (!text || text.trim().length === 0) return;
        
        // Avoid repeating long forms
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA') {
            text = "Input field for " + (e.target.placeholder || e.target.name);
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
        // Listen on hoverable selectors
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
