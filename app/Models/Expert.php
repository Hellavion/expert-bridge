<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expert extends Model
{
    protected $fillable = [
        'name',
        'telegram_login',
        'comment',
        'commission_percent',
        'expert_bonus',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'commission_percent' => 'decimal:2',
    ];

    public function curators(): HasMany
    {
        return $this->hasMany(Curator::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function promocodes(): HasMany
    {
        return $this->hasMany(Promocode::class);
    }
}
