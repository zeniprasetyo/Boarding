<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Halaqah extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'halaqah';

    protected $fillable = [
        'kode',
        'nama_halaqah',
        'musyrif_id',
    ];

    public function musyrif()
    {
        return $this->belongsTo(User::class, 'musyrif_id');
    }
}
