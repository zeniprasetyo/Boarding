<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Absen extends Model
{
    use SoftDeletes;

    protected $table = 'absen';

    protected $fillable = [
        'kode',
        'santri_id',
        'tanggal',
        'pagi',
        'malam',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'pagi' => 'string',
        'malam' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // RELASI KE SANTRI (USER)
    public function santri()
    {
        return $this->belongsTo(User::class, 'santri_id', 'id');
    }
}