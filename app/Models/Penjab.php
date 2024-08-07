<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjab extends Model
{
    use HasFactory;
    protected $table = "penjab";

    function regPeriksa()
    {
        return $this->hasMany(RegPeriksa::class, 'kd_pj', 'kd_pj');
    }
    function jnsPerawatan()
    {
        return $this->hasMany(JnsPerawatan::class, 'kd_pj', 'kd_pj');
    }
	function scopeActive($query, $status = '1')
	{
		$query->where('status', $status);
	}

}
