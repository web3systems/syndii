<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chatbot extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'chatbots';

    public function conversations()
    {
        return $this->hasMany(ChatbotConversation::class, 'chatbot_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatbotHistory::class, 'chatbot_id');
    }

}
