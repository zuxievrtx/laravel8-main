<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    // Definisikan nama table
    protected $table = 'siswa';

    // List Kolom yang boleh diisi 
    protected $fillable = [
        'nama',
        'alamat',
        'email',
        'nohp',
        'foto'
    ];

    // Jika tidak ingin kolom created_at dan updated_at ada
    public $timestamps = false;
}
