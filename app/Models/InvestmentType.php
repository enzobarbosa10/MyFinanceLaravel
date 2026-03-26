<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvestmentType extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];

    public function assets(): HasMany
    {
        return $this->hasMany(InvestmentAsset::class, 'type_id');
    }
}
