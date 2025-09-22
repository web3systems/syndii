<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeechProResult extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'speech_pro_results';

    protected $casts = [
        'transcript' => 'array',
        'raw' => 'array',
        'export_files' => 'array',
    ];
}
