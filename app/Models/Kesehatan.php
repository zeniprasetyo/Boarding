<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kesehatan extends Model
{
    use SoftDeletes;

    protected $table = 'kesehatan';

    protected $fillable = [
        'kode',
        'santri_id',
        'tanggal',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // RELASI KE USER/SANTRI
    public function santri()
    {
        return $this->belongsTo(\App\Models\User::class, 'santri_id', 'id');
    }
}