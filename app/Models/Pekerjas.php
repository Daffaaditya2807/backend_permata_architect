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
}
