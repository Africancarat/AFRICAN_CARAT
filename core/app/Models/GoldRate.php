<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldRate extends Model
{
    public $timestamps = false;

    protected $table = 'gold_rates';

    protected $fillable = [
        'karat',
        'rate_per_gram',
    ];

    protected $casts = [
        'rate_per_gram' => 'decimal:2',
    ];
}
