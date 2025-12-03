<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curator extends Model
{
    protected $fillable = [
        'name',
        'telegram_login',
        'curator_bonus',
        'expert_id',
        'comment',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class);
    }

    public function promocodes(): HasMany
    {
        return $this->hasMany(Promocode::class);
    }
}
