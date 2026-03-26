{{-- Solução Section --}}
<section class="py-20 lg:py-28 bg-gradient-to-b from-emerald-50/50 to-white">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            {{-- Texto --}}
            <div>
                <span class="text-sm font-bold uppercase tracking-wider text-emerald-600">Chega de caos financeiro</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold tracking-tight">Em 2 minutos, você sai do escuro<br>e enxerga cada centavo</h2>
                <p class="mt-5 text-lg text-slate-500 leading-relaxed">
                    Esqueça planilhas que dão sono e apps que você abandona em uma semana. O MyFinance foi feito para quem quer <strong class="text-slate-700">respostas rápidas, clareza total e resultados de verdade</strong> — sem precisa ser expert em finanças.
                </p>
                <ul class="mt-8 space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center mt-0.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-slate-600"><strong class="text-slate-800">Crie sua conta em 2 minutos</strong> — só nome, e-mail e senha. Zero burocracia.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center mt-0.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-slate-600"><strong class="text-slate-800">Veja tudo de relance</strong> — um painel visual que você entende em 5 segundos.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center mt-0.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-slate-600"><strong class="text-slate-800">Gastos, metas, dívidas e investimentos</strong> — tudo num só lugar, sem alternar entre apps.</span>
                    </li>
                </ul>
                <div class="mt-10">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-emerald-600 text-white font-bold px-7 py-3.5 rounded-xl hover:bg-emerald-700 transition shadow-lg shadow-emerald-600/20">
                            Quero sair do vermelho agora
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    @endif
                </div>
            </div>
            {{-- Visual --}}
            <div class="relative">
                <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 space-y-4">
                    {{-- Mini cards --}}
                    <div class="flex items-center gap-4 bg-emerald-50 rounded-xl p-4">
                        <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold text-sm">+</div>
                        <div class="flex-1"><div class="text-sm font-semibold">Salário recebido</div><div class="text-xs text-slate-400">Hoje, 08:30</div></div>
                        <div class="text-emerald-600 font-bold">+R$ 5.200</div>
                    </div>
                    <div class="flex items-center gap-4 bg-white rounded-xl p-4 border border-slate-100">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-500 font-bold text-sm">−</div>
                        <div class="flex-1"><div class="text-sm font-semibold">Supermercado</div><div class="text-xs text-slate-400">Ontem, 14:20</div></div>
                        <div class="text-red-500 font-bold">−R$ 342</div>
                    </div>
                    <div class="flex items-center gap-4 bg-white rounded-xl p-4 border border-slate-100">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 font-bold text-sm">−</div>
                        <div class="flex-1"><div class="text-sm font-semibold">Conta de luz</div><div class="text-xs text-slate-400">22 Mar, 10:00</div></div>
                        <div class="text-red-500 font-bold">−R$ 189</div>
                    </div>
                    {{-- Progress bar --}}
                    <div class="bg-slate-50 rounded-xl p-4 mt-2">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold">Meta: Viagem para o Nordeste</span>
                            <span class="text-xs font-bold text-emerald-600">72%</span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: 72%"></div>
                        </div>
                        <div class="mt-1 text-xs text-slate-400">R$ 3.600 de R$ 5.000</div>
                    </div>
                </div>
                {{-- Floating accent --}}
                <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-emerald-200/50 rounded-full blur-2xl pointer-events-none"></div>
            </div>
        </div>
    </div>
</section>
