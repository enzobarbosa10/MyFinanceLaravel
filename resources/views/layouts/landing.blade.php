<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'MyFinance — De endividado a no controle: organize suas finanças em minutos')</title>
    <meta name="description" content="@yield('meta_description', 'Milhares de brasileiros já saíram do vermelho com o MyFinance. Veja pra onde seu dinheiro vai, elimine dívidas e guarde mais. Comece grátis em 2 minutos.')">
    @yield('meta')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="font-[Inter] text-slate-900 antialiased">

    @include('components.landing.navbar')

    @yield('content')

    @include('components.landing.footer')

    @stack('scripts')
</body>
</html>
