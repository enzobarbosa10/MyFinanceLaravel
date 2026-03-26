{{-- CTA Final Section --}}
<section class="py-16 lg:py-20 px-5 lg:px-8">
    <div class="max-w-4xl mx-auto relative overflow-hidden bg-slate-900 rounded-3xl px-8 py-16 sm:px-16 sm:py-20 text-center">
        {{-- Glow --}}
        <div class="absolute -top-20 -right-20 w-60 h-60 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-emerald-400/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Cada dia sem controle é dinheiro jogado fora.<br>Comece agora em 2 minutos. É grátis.</h2>
            <p class="mt-4 text-lg text-slate-400 max-w-lg mx-auto">Junte-se a +15 mil brasileiros que pararam de perder dinheiro. Você só precisa de nome, e-mail e senha.</p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-emerald-500 text-white font-bold text-lg px-8 py-4 rounded-xl hover:bg-emerald-400 transition-all duration-200 shadow-xl shadow-emerald-500/30">
                        Quero assumir o controle agora
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                @endif
            </div>
            <p class="mt-5 text-sm text-slate-500">Sem cartão de crédito. Sem compromisso. Sem letra miuda. Cancele quando quiser.</p>
        </div>
    </div>
</section>
