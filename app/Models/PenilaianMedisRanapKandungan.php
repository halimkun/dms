<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianMedisRanapKandungan extends Model
{
    use HasFactory;
    
    protected $table = 'penilaian_medis_ranap_kandungan';

    public $timestamps = false;
}
