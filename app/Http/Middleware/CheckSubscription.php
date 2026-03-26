<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->subscription) {
            return redirect()->route('planos')
                ->with('error', 'Você precisa de um plano ativo para acessar esta funcionalidade.');
        }

        return $next($request);
    }
}
