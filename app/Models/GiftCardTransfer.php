<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCardTransfer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'gift_card_transfers';
}
