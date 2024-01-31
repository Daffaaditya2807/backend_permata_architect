<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensis extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_absensi';
    protected $table = 'absensis';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pekerja',
        'status_absensi',
        'id_proyek',
        'tanggal'
    ];

    public function pekerja()
    {
        return $this->belongsTo(Pekerjas::class, 'id_pekerja', 'id_pekerja');
    }
}
