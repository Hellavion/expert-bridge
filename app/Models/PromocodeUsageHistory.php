<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromocodeUsageHistory extends Model
{
    protected $table = 'promocode_usage_history';

    protected $fillable = [
        'promocode_id',
        'client_id',
    ];

    public function promocode(): BelongsTo
    {
        return $this->belongsTo(Promocode::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
