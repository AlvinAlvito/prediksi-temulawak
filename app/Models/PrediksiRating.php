<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrediksiRating extends Model
{
    protected $table = 'prediksi_ratings';
    protected $fillable = [
        'dataset_name', 'user_label', 'item_kode', 'prediksi',
        'neighbors_used', 'denominator'
    ];
}
