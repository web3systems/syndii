<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotEmbedding extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'chatbot_embeddings';
}
