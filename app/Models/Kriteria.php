<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    protected $fillable = ['kode', 'nama'];

    public function subkriterias(): HasMany
    {
        return $this->hasMany(Subkriteria::class);
    }
}
