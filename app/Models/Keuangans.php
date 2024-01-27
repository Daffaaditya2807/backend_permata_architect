<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keuangans extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pengeluaran',
        'pemasukan',
        'id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}
