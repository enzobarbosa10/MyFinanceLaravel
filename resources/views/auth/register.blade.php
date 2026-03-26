<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro — MyFinance</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-body">
    <div class="auth-card">
        <div class="auth-brand">
            <h1>MyFinance</h1>
            <p>Crie sua conta</p>
        </div>
        @if($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <label for="name">Nome</label>
            <input type="text" id="name" name="name" required autofocus value="{{ old('name') }}" placeholder="Seu nome completo">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="seu@email.com">

            <label for="password">Senha (mínimo 8 caracteres)</label>
            <input type="password" id="password" name="password" required minlength="8" placeholder="Crie uma senha segura">

            <label for="password_confirmation">Confirmar Senha</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" placeholder="Repita a senha">

            <button type="submit" class="btn-auth">Cadastrar</button>
        </form>
        <div class="auth-footer">
            Já tem conta? <a href="{{ route('login') }}">Faça login</a>
        </div>
    </div>
</body>
</html>
