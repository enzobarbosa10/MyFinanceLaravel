<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLevel
{
    /**
     * Verifica se o usuário está no plano mínimo exigido.
     *
     * Uso nas rotas:
     *   ->middleware('plan.level:pro')
     *   ->middleware('plan.level:premium')
     */
    public function handle(Request $request, Closure $next, string $minimumPlan): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if (! $user->onPlanOrHigher($minimumPlan)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Esta funcionalidade requer o plano {$minimumPlan} ou superior.",
                    'required_plan' => $minimumPlan,
                    'upgrade_url' => route('planos'),
                ], 403);
            }

            return redirect()->route('planos')
                ->with('error', "Esta funcionalidade requer o plano {$minimumPlan} ou superior.");
        }

        return $next($request);
    }
}
