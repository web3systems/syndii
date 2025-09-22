<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CookieSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'cookie_settings';
}
