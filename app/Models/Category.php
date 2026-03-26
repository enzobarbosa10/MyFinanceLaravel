<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name', 'type'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public static function seedDefaults(int $userId): void
    {
        $defaults = [
            ['Salário', TransactionType::Entrada],
            ['Freelance', TransactionType::Entrada],
            ['Rendimentos', TransactionType::Entrada],
            ['Outros (entrada)', TransactionType::Entrada],
            ['Alimentação', TransactionType::Saida],
            ['Transporte', TransactionType::Saida],
            ['Moradia', TransactionType::Saida],
            ['Saúde', TransactionType::Saida],
            ['Educação', TransactionType::Saida],
            ['Lazer', TransactionType::Saida],
            ['Outros (saída)', TransactionType::Saida],
        ];

        foreach ($defaults as [$name, $type]) {
            self::create([
                'user_id' => $userId,
                'name' => $name,
                'type' => $type,
            ]);
        }
    }
}
