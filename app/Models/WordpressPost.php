<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WordpressPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'website_name',
        'website_id',
        'platform',
        'title',
        'content',
        'excerpt',
        'slug',
        'categories',
        'tags',
        'featured_image',
        'status',
        'scheduled_at',
        'published_at',
        'post_id',
        'post_url',
        'custom_fields',
        'error_message',
        'post_status',
        'published_at',
        'scheduled_at',
    ];
    
    /**
     * Get the user that owns the scheduled post
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the website integration for this post
     */
    public function website()
    {
        return $this->belongsTo(UserIntegration::class, 'website_id');
    }
}
