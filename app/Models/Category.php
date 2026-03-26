<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
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
            ['Salário', 'entrada'],
            ['Freelance', 'entrada'],
            ['Rendimentos', 'entrada'],
            ['Outros (entrada)', 'entrada'],
            ['Alimentação', 'saida'],
            ['Transporte', 'saida'],
            ['Moradia', 'saida'],
            ['Saúde', 'saida'],
            ['Educação', 'saida'],
            ['Lazer', 'saida'],
            ['Outros (saída)', 'saida'],
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
