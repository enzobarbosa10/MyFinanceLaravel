@extends('layouts.landing')

@section('title', 'Contato — MyFinance')
@section('meta_description', 'Entre em contato com a equipe MyFinance. Estamos prontos para ajudar com dúvidas, sugestões ou parcerias.')

@section('content')
{{-- Hero --}}
<section class="relative pt-28 pb-16 lg:pt-36 lg:pb-20 bg-gradient-to-b from-emerald-50/80 via-white to-white overflow-hidden">
    <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-emerald-200/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-5 lg:px-8 text-center">
        <span class="text-sm font-bold uppercase tracking-wider text-emerald-600">Contato</span>
        <h1 class="mt-4 text-4xl sm:text-5xl font-black tracking-tight">Fale com a gente</h1>
        <p class="mt-5 text-lg text-slate-500 max-w-2xl mx-auto">Dúvidas, sugestões ou quer bater um papo? Nossa equipe responde em até 24 horas.</p>
    </div>
</section>

{{-- Formulário --}}
<section class="py-20 lg:py-28 bg-white">
    <div class="max-w-5xl mx-auto px-5 lg:px-8">
        <div class="grid lg:grid-cols-5 gap-12">

            {{-- Info --}}
            <div class="lg:col-span-2 space-y-8">
                <div>
                    <h3 class="font-bold text-lg mb-2">E-mail</h3>
                    <p class="text-slate-500">contato@myfinance.app</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-2">Suporte</h3>
                    <p class="text-slate-500">suporte@myfinance.app</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-2">Redes sociais</h3>
                    <div class="flex gap-4 mt-2">
                        <a href="#" class="text-slate-400 hover:text-emerald-600 transition" aria-label="Instagram">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="#" class="text-slate-400 hover:text-emerald-600 transition" aria-label="Twitter/X">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="#" class="text-slate-400 hover:text-emerald-600 transition" aria-label="LinkedIn">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-2">Horário</h3>
                    <p class="text-slate-500">Seg a Sex, 9h às 18h (Brasília)</p>
                </div>
            </div>

            {{-- Form --}}
            <div class="lg:col-span-3">
                @if (session('success'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 text-sm font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('contact.send') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nome</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-slate-800 placeholder-slate-400"
                            placeholder="Seu nome completo"
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">E-mail</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-slate-800 placeholder-slate-400"
                            placeholder="seu@email.com"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-semibold text-slate-700 mb-2">Mensagem</label>
                        <textarea
                            id="message"
                            name="message"
                            rows="5"
                            required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-slate-800 placeholder-slate-400 resize-none"
                            placeholder="Como podemos ajudar?"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-emerald-600 text-white font-bold text-base px-8 py-3.5 rounded-xl hover:bg-emerald-700 transition-all duration-200 shadow-lg shadow-emerald-600/20">
                        Enviar mensagem
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>
@endsection
