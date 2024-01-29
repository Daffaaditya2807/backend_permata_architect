<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyeks extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_proyek';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lokasi_proyek',
        'keterangan',
        'nama_pemilik',
        'tanggal_dimulai',
        'tanggal_selesai',
        'status',
        'nilai_proyek'
    ];

    public function pengeluarans()
    {
        return $this->hasMany(Pengeluarans::class, 'id_proyek', 'id_proyek');
    }
    public function getTotalPengeluaranAttribute()
    {
        return $this->pengeluarans()->sum('harga_satuan');
    }

    public function pekerjas()
    {
        return $this->hasMany(pekerjas::class, 'id_proyek', 'id_proyek');
    }

    public function absensis()
    {
        return $this->hasMany(Absensis::class, 'id_proyek', 'id_proyek');
    }

    public function progress()
    {
        return $this->hasMany(Absensis::class, 'id_proyek', 'id_proyek');
    }
}
