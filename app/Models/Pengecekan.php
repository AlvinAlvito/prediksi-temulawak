<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengecekan extends Model
{
    protected $fillable = [
        'dataset_name',
        'nama_pembeli', 'c1','c2','c3','c4','c5','c6'
    ];
    // helper untuk ambil array nilai (memudahkan saat CF nanti)
    public function asVector(): array
    {
        return [
            'C1' => (int)$this->c1,
            'C2' => (int)$this->c2,
            'C3' => (int)$this->c3,
            'C4' => (int)$this->c4,
            'C5' => (int)$this->c5,
            'C6' => (int)$this->c6,
        ];
    }
}
