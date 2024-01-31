<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kasbons extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_kasbon';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pekerja',
        'jumlah_kasbon',
        'id_proyek'
    ];

    public function pekerja()
    {
        return $this->belongsTo(Pekerjas::class, 'id_pekerja', 'id_pekerja');
    }
}
