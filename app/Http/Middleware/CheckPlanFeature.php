<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanFeature
{
    /**
     * Verifica se o plano do usuário possui a feature solicitada.
     *
     * Uso nas rotas:
     *   ->middleware('plan.feature:ai_assistant')
     *   ->middleware('plan.feature:investments')
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if (! $user->canUseFeature($feature)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Seu plano não inclui esta funcionalidade.',
                    'feature' => $feature,
                    'upgrade_url' => route('planos'),
                ], 403);
            }

            return redirect()->route('planos')
                ->with('error', "Seu plano não inclui a funcionalidade \"{$feature}\". Faça upgrade!");
        }

        return $next($request);
    }
}
