<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotDomain extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'chatbot_domains';
}
