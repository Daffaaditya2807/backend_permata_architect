<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukans extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_pemasukan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'jumlah_pemasukan',
        'keterangan',
        'sumber_dana',
        'foto',
        'id',
        'id_proyek'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}
