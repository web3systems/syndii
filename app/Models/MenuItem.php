<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'id',
        'parent_key',
        'order',
        'key',
        'route',
        'route_slug',
        'label',
        'icon',
        'type',
        'svg',
        'is_active',
        'is_admin',
        'extension',
        'url',
        'permission',
        'conditions',
        'badge_text',
        'badge_type'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'conditions' => 'array',
        'children' => 'array',
        'is_active' => 'boolean',
        'is_admin' => 'boolean',
        'extension' => 'boolean',
        'original' => 'boolean',
    ];

}