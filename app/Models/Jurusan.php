<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    /** @use HasFactory<\Database\Factories\JurusanFactory> */
    use HasFactory, HasUuids;


    protected $fillable = [
        'kode_jurusan',
        'nama',
        'created_at',
        'updated_at',
    ];

    public function prodis()
    {
        return $this->hasMany(Prodi::class);
    }
}
