<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Halaqah;
use App\Models\Kegiatan;

class CeklistKegiatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ceklist_kegiatan';

    protected $fillable = [
        'kode',
        'halaqah_id',
        'kegiatan_id',
        'santri_id',
        'tanggal',
        'status',
    ];

    // Relasi ke tabel users bawaan Laravel
    public function santri()
    {
        return $this->belongsTo(User::class, 'santri_id');
    }

    public function halaqah()
    {
        return $this->belongsTo(Halaqah::class, 'halaqah_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->kode)) {
                $model->kode = (string) Str::uuid();
            }
        });
    }
}
