{{-- Hero Section --}}
<section class="relative overflow-hidden pt-28 pb-20 lg:pt-40 lg:pb-32 bg-gradient-to-b from-emerald-50/80 via-white to-white">
    {{-- Decorative blobs --}}
    <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-emerald-200/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-60 -left-40 w-[400px] h-[400px] bg-teal-100/40 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-5 lg:px-8 text-center">
        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-wider px-4 py-1.5 rounded-full mb-6">
            <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span></span>
            Já usado por +15 mil brasileiros
        </div>

        {{-- Headline --}}
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black leading-[1.1] tracking-tight max-w-3xl mx-auto">
            De "pra onde foi meu dinheiro?"<br>
            <span class="text-emerald-600">para "sobrou este mês".</span>
        </h1>

        {{-- Subheadline --}}
        <p class="mt-6 text-lg sm:text-xl text-slate-500 max-w-xl mx-auto leading-relaxed">
            O MyFinance mostra cada centavo que entra e sai, mata suas dívidas e te ajuda a guardar dinheiro de verdade — em menos de 2 minutos por dia.
        </p>

        {{-- CTA Buttons --}}
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-emerald-600 text-white font-bold text-lg px-8 py-4 rounded-xl hover:bg-emerald-700 transition-all duration-200 shadow-xl shadow-emerald-600/25 hover:shadow-emerald-600/40 hover:-translate-y-0.5">
                    Quero organizar meu dinheiro agora
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            @endif
            <a href="#funcionalidades" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-slate-700 font-semibold text-lg px-8 py-4 rounded-xl border-2 border-slate-200 hover:border-emerald-300 hover:text-emerald-700 transition-all duration-200">
                Ver o que o MyFinance faz por você
            </a>
        </div>

        {{-- Social proof mini --}}
        <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-6 text-sm text-slate-400">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                Grátis para sempre no plano Free
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                Pronto em 2 minutos — sem burocracia
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                Seus dados 100% seguros e privados
            </div>
        </div>

        {{-- Dashboard mockup --}}
        <div class="mt-16 max-w-4xl mx-auto">
            <div class="relative rounded-2xl bg-white shadow-2xl shadow-slate-900/10 border border-slate-200 overflow-hidden">
                {{-- Browser bar --}}
                <div class="flex items-center gap-2 px-4 py-3 bg-slate-50 border-b border-slate-200">
                    <div class="flex gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                    </div>
                    <div class="flex-1 mx-4 h-7 bg-white rounded-md border border-slate-200 flex items-center justify-center text-xs text-slate-400">myfinance.app/dashboard</div>
                </div>
                {{-- Dashboard content mockup --}}
                <div class="p-6 sm:p-8 bg-slate-50/50">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div class="bg-white rounded-xl p-4 border border-slate-100">
                            <div class="text-xs text-slate-400 mb-1">Saldo Total</div>
                            <div class="text-lg font-bold text-emerald-600">R$ 12.450,00</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-slate-100">
                            <div class="text-xs text-slate-400 mb-1">Receitas</div>
                            <div class="text-lg font-bold text-slate-800">R$ 8.500,00</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-slate-100">
                            <div class="text-xs text-slate-400 mb-1">Despesas</div>
                            <div class="text-lg font-bold text-red-500">R$ 4.320,00</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-slate-100">
                            <div class="text-xs text-slate-400 mb-1">Economia</div>
                            <div class="text-lg font-bold text-emerald-600">R$ 4.180,00</div>
                        </div>
                    </div>
                    {{-- Chart placeholder --}}
                    <div class="bg-white rounded-xl border border-slate-100 p-6">
                        <div class="flex items-end justify-around h-32 gap-2">
                            <div class="w-full bg-emerald-100 rounded-t-md" style="height:40%"></div>
                            <div class="w-full bg-emerald-200 rounded-t-md" style="height:55%"></div>
                            <div class="w-full bg-emerald-300 rounded-t-md" style="height:70%"></div>
                            <div class="w-full bg-emerald-400 rounded-t-md" style="height:45%"></div>
                            <div class="w-full bg-emerald-500 rounded-t-md" style="height:85%"></div>
                            <div class="w-full bg-emerald-600 rounded-t-md" style="height:100%"></div>
                            <div class="w-full bg-emerald-300 rounded-t-md" style="height:60%"></div>
                        </div>
                        <div class="flex justify-around mt-2 text-[10px] text-slate-400">
                            <span>Jan</span><span>Fev</span><span>Mar</span><span>Abr</span><span>Mai</span><span>Jun</span><span>Jul</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
