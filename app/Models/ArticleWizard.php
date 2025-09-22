<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleWizard extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'article_wizards';

    protected $casts = [
        'outlines' => 'array'
    ];
}


