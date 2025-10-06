<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subkriteria extends Model
{
    protected $fillable = ['kriteria_id', 'label', 'bobot', 'urutan', 'keterangan'];

    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class);
    }
}
