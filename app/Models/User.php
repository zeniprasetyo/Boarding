<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'telephone', // Tambahkan field telephone di sini
        'password',
        'halaqah_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke Halaqah
     */
    public function halaqah()
    {
        return $this->belongsTo(Halaqah::class, 'halaqah_id');
    }

    /**
     * Relasi ke ceklist kegiatan (kalau user = santri)
     */
    public function ceklistKegiatan()
    {
        return $this->hasMany(CeklistKegiatan::class, 'santri_id');
    }
    
    public function absen()
    {
        return $this->hasMany(Absen::class, 'kode', 'id');
    }
    
    public function kesehatan()
    {
        return $this->hasMany(\App\Models\Kesehatan::class, 'kode', 'id');
    }
}