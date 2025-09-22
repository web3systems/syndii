<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AzureModel extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'azure_models';
}
