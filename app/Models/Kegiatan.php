<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kegiatan extends Model
{
    use HasFactory, SoftDeletes;

    /** Nama tabel */
    protected $table = 'kegiatan';

    /** Kolom yang bisa diisi massal */
    protected $fillable = [
        'kode',
        'nama_kegiatan',
        'parent_id',
    ];

    /** Tanggal yang dikelola sebagai Carbon instance */
    protected $dates = ['deleted_at'];

    /** 🔹 Relasi ke kegiatan induk */
    public function parent()
    {
        return $this->belongsTo(Kegiatan::class, 'parent_id');
    }

    /** 🔹 Relasi ke kegiatan anak */
    public function children()
    {
        return $this->hasMany(Kegiatan::class, 'parent_id');
    }

    /** 🔹 Generate kode otomatis seperti KG0001, KG0002, dst */
    public static function generateKode()
    {
        $last = self::withTrashed()->orderBy('id', 'desc')->first();
        $number = $last ? intval(substr($last->kode, -4)) + 1 : 1;
        return 'KG' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
