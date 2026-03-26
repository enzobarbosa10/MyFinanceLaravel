<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFeature extends Model
{
    public $timestamps = false;
    protected $fillable = ['plan_id', 'feature', 'limit_value'];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
