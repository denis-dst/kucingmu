@if(session()->has('impersonator_id'))
    <div class="bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 text-white shadow-md z-50 sticky top-0 border-b border-amber-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 sm:py-2.5 flex flex-wrap items-center justify-between gap-3 text-xs sm:text-sm">
            <div class="flex items-center gap-2">
                <span class="animate-pulse text-base">🎭</span>
                <span class="font-bold">Mode Impersonasi Aktif:</span>
                <span class="text-amber-100">
                    Anda sedang mengakses sistem sebagai 
                    <strong class="text-white underline">{{ Auth::user()->name }}</strong> 
                    (<span class="uppercase font-semibold tracking-wider text-[11px] bg-white/20 px-1.5 py-0.5 rounded">{{ Auth::user()->role }}</span>).
                </span>
            </div>
            
            <form action="{{ route('impersonate.leave') }}" method="POST" class="inline-flex">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 bg-white text-amber-900 hover:bg-amber-50 active:scale-[0.98] font-bold px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-lg shadow-sm text-xs transition">
                    <span>↩️</span>
                    <span>Kembali ke Akun Admin</span>
                </button>
            </form>
        </div>
    </div>
@endif
