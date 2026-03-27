<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MyFinance')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @php
        $currentPath = request()->path();
        $userName = auth()->user()->name ?? '';
        $parts = array_filter(explode(' ', trim($userName)));
        $initials = count($parts) >= 2
            ? mb_strtoupper(mb_substr(reset($parts), 0, 1) . mb_substr(end($parts), 0, 1))
            : mb_strtoupper(mb_substr($userName, 0, 2));
    @endphp

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">MyFinance</div>
        <a class="nav-item {{ $currentPath === '/' ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <span class="nav-icon">⊞</span> Dashboard
        </a>
        <a class="nav-item {{ str_starts_with($currentPath, 'transactions') ? 'active' : '' }}" href="{{ route('transactions.index') }}">
            <span class="nav-icon">↕</span> Transações
        </a>
        <a class="nav-item {{ str_starts_with($currentPath, 'accounts') ? 'active' : '' }}" href="{{ route('accounts.index') }}">
            <span class="nav-icon">💳</span> Contas
        </a>
        <a class="nav-item {{ str_starts_with($currentPath, 'goals') ? 'active' : '' }}" href="{{ route('goals.index') }}">
            <span class="nav-icon">🎯</span> Metas
        </a>
        <a class="nav-item {{ str_starts_with($currentPath, 'budgets') ? 'active' : '' }}" href="{{ route('budgets.index') }}">
            <span class="nav-icon">📊</span> Orçamentos
        </a>
        <a class="nav-item {{ str_starts_with($currentPath, 'debts') ? 'active' : '' }}" href="{{ route('debts.index') }}">
            <span class="nav-icon">💰</span> Dívidas
        </a>
        <a class="nav-item {{ str_starts_with($currentPath, 'investments') ? 'active' : '' }}" href="{{ route('investments.index') }}">
            <span class="nav-icon">📈</span> Investimentos
        </a>
        <a class="nav-item {{ str_starts_with($currentPath, 'open-finance') ? 'active' : '' }}" href="{{ route('open-finance.index') }}">
            <span class="nav-icon">🏦</span> Open Finance
        </a>
        <div class="sidebar-spacer"></div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item" style="width:100%;background:none;border:none;cursor:pointer;text-align:left;color:var(--text-secondary);font-family:inherit;font-size:0.875rem;font-weight:500;">
                <span class="nav-icon">↪</span> Sair
            </button>
        </form>
        <div class="sidebar-user">
            <div class="user-avatar">{{ $initials }}</div>
            <div>
                <div class="user-name">{{ $userName }}</div>
            </div>
        </div>
    </aside>

    <!-- MOBILE SIDEBAR TOGGLE -->
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
        });
    </script>
    @stack('scripts')
</body>
</html>
