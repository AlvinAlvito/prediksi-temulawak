<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSimilarity extends Model
{
    protected $fillable = ['item_i', 'item_j', 'similarity', 'computed_at'];
}
