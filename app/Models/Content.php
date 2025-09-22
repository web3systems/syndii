<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Content extends Model implements Searchable
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'contents';

    public function getSearchResult(): SearchResult
    {
        return new SearchResult(
            $this,
            $this->title ?? 'Untitled Content',
            $this->id
        );
    }
}
