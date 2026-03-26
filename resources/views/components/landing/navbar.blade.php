{{-- Navbar --}}
<nav id="navbar" class="fixed top-0 inset-x-0 z-50 bg-white/90 backdrop-blur-lg border-b border-slate-100 transition-shadow duration-300">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-5 py-4 lg:px-8">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-emerald-600 font-extrabold text-xl tracking-tight">
            <svg class="w-8 h-8" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="8" class="fill-emerald-600"/>
                <path d="M9 22V14M16 22V10M23 22V16" stroke="white" stroke-width="3" stroke-linecap="round"/>
            </svg>
            MyFinance
        </a>

        {{-- Desktop links --}}
        <div class="hidden md:flex items-center gap-8">
            <a href="{{ route('features') }}" class="text-sm font-medium text-slate-500 hover:text-slate-900 transition">Funcionalidades</a>
            <a href="{{ route('benefits') }}" class="text-sm font-medium text-slate-500 hover:text-slate-900 transition">Benefícios</a>
            <a href="{{ route('testimonials') }}" class="text-sm font-medium text-slate-500 hover:text-slate-900 transition">Depoimentos</a>
            <a href="{{ route('blog.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-900 transition">Blog</a>
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-emerald-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-emerald-700 transition shadow-lg shadow-emerald-600/20">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-emerald-600 transition">Entrar</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-emerald-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-emerald-700 transition shadow-lg shadow-emerald-600/20">Começar grátis</a>
                    @endif
                @endauth
            @endif
        </div>

        {{-- Mobile toggle --}}
        <button id="menuToggle" class="md:hidden flex flex-col gap-1.5 p-2" aria-label="Menu">
            <span class="block w-6 h-0.5 bg-slate-800 transition-transform" id="bar1"></span>
            <span class="block w-6 h-0.5 bg-slate-800 transition-opacity" id="bar2"></span>
            <span class="block w-6 h-0.5 bg-slate-800 transition-transform" id="bar3"></span>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-slate-100 px-5 pb-6 pt-2 space-y-4">
        <a href="{{ route('features') }}" class="block text-sm font-medium text-slate-600 hover:text-emerald-600">Funcionalidades</a>
        <a href="{{ route('benefits') }}" class="block text-sm font-medium text-slate-600 hover:text-emerald-600">Benefícios</a>
        <a href="{{ route('testimonials') }}" class="block text-sm font-medium text-slate-600 hover:text-emerald-600">Depoimentos</a>
        <a href="{{ route('blog.index') }}" class="block text-sm font-medium text-slate-600 hover:text-emerald-600">Blog</a>
        @if (Route::has('login'))
            @auth
                <a href="{{ route('dashboard') }}" class="block w-full text-center bg-emerald-600 text-white font-semibold py-3 rounded-lg">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block text-sm font-semibold text-slate-700">Entrar</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="block w-full text-center bg-emerald-600 text-white font-semibold py-3 rounded-lg">Começar grátis</a>
                @endif
            @endauth
        @endif
    </div>
</nav>
