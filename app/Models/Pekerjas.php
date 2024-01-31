<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pekerjas extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_pekerja';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_pekerja',
        'role',
        'upah',
        'id_proyek'
    ];

    public function absensis()
    {
        return $this->hasMany(Absensis::class, 'id_pekerja', 'id_pekerja');
    }

    public function kasbons()
    {
        return $this->hasMany(Kasbons::class, 'id_pekerja', 'id_pekerja');
    }
}
