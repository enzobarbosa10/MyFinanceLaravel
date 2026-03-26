<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — MyFinance</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-body">
    <div class="auth-card">
        <div class="auth-brand">
            <h1>MyFinance</h1>
            <p>Acesse sua conta</p>
        </div>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autofocus placeholder="seu@email.com" value="{{ old('email') }}">

            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required placeholder="Sua senha">

            <button type="submit" class="btn-auth">Entrar</button>
        </form>
        <div class="auth-footer">
            Não tem conta? <a href="{{ route('register') }}">Cadastre-se</a>
        </div>
    </div>
</body>
</html>
