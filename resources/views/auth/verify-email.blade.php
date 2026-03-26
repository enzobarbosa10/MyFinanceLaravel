<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar E-mail — MyFinance</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-body">
    <div class="auth-card">
        <div class="auth-brand">
            <h1>MyFinance</h1>
            <p>Verifique seu e-mail</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <p style="text-align:center; margin: 1rem 0;">
            Antes de continuar, verifique seu e-mail clicando no link que enviamos para
            <strong>{{ Auth::user()->email }}</strong>.
        </p>

        <p style="text-align:center; margin-bottom: 1.5rem; color: #666;">
            Se não recebeu o e-mail, clique no botão abaixo para reenviar.
        </p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-auth">Reenviar e-mail de verificação</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" style="margin-top: 1rem; text-align: center;">
            @csrf
            <button type="submit" style="background: none; border: none; color: #666; cursor: pointer; text-decoration: underline;">
                Sair
            </button>
        </form>
    </div>
</body>
</html>
