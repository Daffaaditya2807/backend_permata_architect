<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluarans extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_pengeluaran';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'judul_pengeluaran',
        'keterangan',
        'sumber_dana',
        'foto',
        'id',
        'id_proyek',
        'satuan',
        'quantity',
        'harga_satuan',
        'total_pengeluaran',
        'vendor'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}
