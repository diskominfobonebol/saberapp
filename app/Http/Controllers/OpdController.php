<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use Illuminate\Http\Request;

class OpdController extends Controller
{
    public function getOpdSummary()
    {
        $opds = Opd::withCount('pegawai')->get();


        $data = $opds->map(function ($opd) {
            return [
                'opd_id' => $opd->id,
                'nama_opd' => $opd->nama_opd,
                'total_pegawai' => $opd->pegawai_count,
            ];
        });

        $total_pegawai_semua_opd = $opds->sum('pegawai_count');

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Opd summary berhasil diambil',
            'data' => $data,
            'total_pegawai_semua_opd' => $total_pegawai_semua_opd,
            'total_opd' => $opds->count(),
        ]);
    }
}
