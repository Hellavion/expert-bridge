<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'telegram_login',
        'promocode_id',
        'is_paid',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
    ];

    public function promocode(): BelongsTo
    {
        return $this->belongsTo(Promocode::class);
    }

    public function usageHistory(): HasMany
    {
        return $this->hasMany(PromocodeUsageHistory::class);
    }
}
