<?php

namespace App\Services;

use App\Enums\GoalStatus;
use App\Models\Goal;
use App\Models\GoalContribution;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GoalService
{
    // ── Contribuições ────────────────────────────────────────

    public function contribute(Goal $goal, float $amount, ?string $notes = null): GoalContribution
    {
        return DB::transaction(function () use ($goal, $amount, $notes) {
            $contribution = GoalContribution::create([
                'goal_id' => $goal->id,
                'amount' => $amount,
                'contributed_at' => now()->toDateString(),
                'notes' => $notes,
            ]);

            $goal->increment('current_amount', $amount);

            if ($goal->fresh()->current_amount >= $goal->target_amount) {
                $goal->update(['status' => GoalStatus::Completed]);
            }

            return $contribution;
        });
    }

    // ── Cálculos ─────────────────────────────────────────────

    /**
     * Meses restantes até o prazo (mínimo 0).
     */
    public function remainingMonths(Goal $goal): int
    {
        return max(0, (int) now()->startOfMonth()->diffInMonths($goal->deadline->startOfMonth()));
    }

    /**
     * Valor mensal necessário para atingir a meta no prazo.
     */
    public function monthlyRequired(Goal $goal): float
    {
        $remaining = $goal->target_amount - $goal->current_amount;

        if ($remaining <= 0) {
            return 0;
        }

        $months = $this->remainingMonths($goal);

        if ($months <= 0) {
            return $remaining; // precisa do valor total agora
        }

        return round($remaining / $months, 2);
    }

    /**
     * Média mensal efetiva de contribuições.
     */
    public function monthlyAverage(Goal $goal): float
    {
        $firstContribution = $goal->contributions()->min('contributed_at');

        if (!$firstContribution) {
            return 0;
        }

        $monthsActive = max(1, (int) Carbon::parse($firstContribution)->startOfMonth()->diffInMonths(now()->startOfMonth()) + 1);

        return round((float) $goal->current_amount / $monthsActive, 2);
    }

    // ── Progresso ────────────────────────────────────────────

    /**
     * Progresso esperado (%) baseado no tempo decorrido desde a criação.
     */
    public function expectedProgress(Goal $goal): float
    {
        $created = $goal->created_at->startOfMonth();
        $deadline = $goal->deadline->startOfMonth();
        $totalMonths = max(1, (int) $created->diffInMonths($deadline));
        $elapsed = max(0, (int) $created->diffInMonths(now()->startOfMonth()));

        return min(100, round(($elapsed / $totalMonths) * 100, 1));
    }

    /**
     * Avalia o progresso da meta.
     *
     * Retorna: 'on_track', 'ahead', 'behind', 'overdue', 'completed'
     */
    public function evaluateProgress(Goal $goal): string
    {
        if ($goal->status === GoalStatus::Completed || $goal->is_completed) {
            return 'completed';
        }

        if ($goal->is_overdue) {
            return 'overdue';
        }

        return $this->compareProgress($goal);
    }

    private function compareProgress(Goal $goal): string
    {
        $actual = $goal->progressPercentage();
        $expected = $this->expectedProgress($goal);
        $diff = $actual - $expected;

        if ($diff >= 5) {
            return 'ahead';
        }

        return $diff < -5 ? 'behind' : 'on_track';
    }

    // ── Alertas ──────────────────────────────────────────────

    /**
     * Gera alertas para uma meta ativa.
     *
     * @return array<array{type: string, message: string}>
     */
    public function getAlerts(Goal $goal): array
    {
        if ($goal->status !== GoalStatus::Active) {
            return [];
        }

        $alerts = [];
        $progress = $this->evaluateProgress($goal);
        $monthlyReq = $this->monthlyRequired($goal);
        $monthlyAvg = $this->monthlyAverage($goal);
        $remainingMonths = $this->remainingMonths($goal);

        // Meta vencida
        if ($progress === 'overdue') {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'O prazo desta meta já expirou! Faltam R$ ' . number_format($goal->remaining_amount, 2, ',', '.') . ' para concluir.',
            ];
            return $alerts;
        }

        // Atrasado no progresso
        if ($progress === 'behind') {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Você está atrás do ritmo ideal. Progresso atual: ' . number_format($goal->progressPercentage(), 0) . '%, esperado: ' . number_format($this->expectedProgress($goal), 0) . '%.',
            ];
        }

        // Média mensal insuficiente
        if ($monthlyAvg > 0 && $monthlyReq > 0 && $monthlyAvg < $monthlyReq * 0.8) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Sua média mensal (R$ ' . number_format($monthlyAvg, 2, ',', '.') . ') está abaixo do necessário (R$ ' . number_format($monthlyReq, 2, ',', '.') . '/mês).',
            ];
        }

        // Prazo curto
        if ($remainingMonths <= 2 && $remainingMonths > 0 && $goal->remaining_amount > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => 'Restam apenas ' . $remainingMonths . ' ' . ($remainingMonths === 1 ? 'mês' : 'meses') . '! Você precisa de R$ ' . number_format($monthlyReq, 2, ',', '.') . '/mês.',
            ];
        }

        // Adiantado
        if ($progress === 'ahead') {
            $alerts[] = [
                'type' => 'success',
                'message' => 'Parabéns! Você está acima do ritmo esperado. Continue assim!',
            ];
        }

        return $alerts;
    }

    // ── Análise Completa ─────────────────────────────────────

    /**
     * Retorna análise completa de uma meta.
     */
    public function getAnalysis(Goal $goal): array
    {
        return [
            'monthly_required' => $this->monthlyRequired($goal),
            'monthly_average' => $this->monthlyAverage($goal),
            'remaining_months' => $this->remainingMonths($goal),
            'expected_progress' => $this->expectedProgress($goal),
            'progress_status' => $this->evaluateProgress($goal),
            'alerts' => $this->getAlerts($goal),
        ];
    }

    /**
     * Data estimada de conclusão com base na média mensal.
     */
    public function estimatedCompletionDate(Goal $goal): ?Carbon
    {
        $avg = $this->monthlyAverage($goal);

        if ($avg <= 0) {
            return null;
        }

        $remainingAmount = $goal->target_amount - $goal->current_amount;

        if ($remainingAmount <= 0) {
            return now();
        }

        $monthsNeeded = ceil($remainingAmount / $avg);

        return now()->addMonths((int) $monthsNeeded);
    }
}
