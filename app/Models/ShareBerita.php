<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareBerita extends Model
{
    protected $table = 'share_beritas';

    protected $guarded = [];


    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
