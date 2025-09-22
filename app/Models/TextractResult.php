<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextractResult extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'textract_results';
}
