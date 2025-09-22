<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'gift_cards';

    protected $casts = [
        'valid_until' => 'datetime',
    ];
}
