@extends('layouts.app')
@section('title', 'Planos — MyFinance')

@section('content')
<div class="card" style="max-width:600px;margin:2rem auto;text-align:center;">
    <h2>🚀 Funcionalidade Premium</h2>
    <p style="color:var(--text-secondary);margin:1rem 0;">
        Para acessar <strong>Investimentos</strong> e <strong>Metas</strong>, você precisa de um plano ativo.
    </p>

    <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:1.5rem;margin:1.5rem 0;">
        <h3 style="margin-bottom:0.5rem;">Plano Premium</h3>
        <p style="font-size:2rem;font-weight:700;color:var(--accent);margin:0.5rem 0;">
            R$ 19,90<span style="font-size:0.875rem;font-weight:400;color:var(--text-secondary);">/mês</span>
        </p>
        <ul style="list-style:none;padding:0;margin:1rem 0;text-align:left;">
            <li style="padding:0.4rem 0;">✅ Controle de Investimentos</li>
            <li style="padding:0.4rem 0;">✅ Metas Financeiras ilimitadas</li>
            <li style="padding:0.4rem 0;">✅ Alertas de Orçamento</li>
            <li style="padding:0.4rem 0;">✅ Relatórios avançados</li>
        </ul>
    </div>

    <p style="color:var(--text-secondary);font-size:0.875rem;">
        Em breve você poderá assinar diretamente por aqui. Entre em contato para mais informações.
    </p>

    <div class="form-actions" style="justify-content:center;margin-top:1.5rem;">
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Voltar ao Dashboard</a>
    </div>
</div>
@endsection
