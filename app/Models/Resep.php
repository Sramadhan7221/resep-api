<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasFactory;

    protected $table = "tbl_resep";
    protected $guarded = "id";
    protected $fillable = [
        'nama_resep',
        'desc_resep',
        'langkah'
    ];

    public function bahans()
    {
        return $this->hasMany(Bahan::class, 'resep_id', 'id');
    }
}
