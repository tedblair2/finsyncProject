<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documentation extends Model
{
    //
    protected $fillable = [
        'slug',
        'title',
        'content',
        'order_index',
        'is_publishing',
        'created_by',
        'updated_by',
        'can_edit'
    ];
}
