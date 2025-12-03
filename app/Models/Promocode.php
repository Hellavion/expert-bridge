<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promocode extends Model
{
    protected $fillable = [
        'code',
        'expert_id',
        'curator_id',
        'is_active',
        'usage_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'usage_count' => 'integer',
    ];

    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class);
    }

    public function curator(): BelongsTo
    {
        return $this->belongsTo(Curator::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function usageHistory(): HasMany
    {
        return $this->hasMany(PromocodeUsageHistory::class);
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }
}
