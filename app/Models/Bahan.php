<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    use HasFactory;

    protected $table = "tbl_bahan";
    protected $guarded = "id";
    protected $fillable = [
        'resep_id',
        'desc_bahan'
    ];
}
