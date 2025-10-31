<?php

namespace App\Http\Controllers;

use App\Http\Resources\OpdSummaryResource;
use App\Http\Resources\SummaryOpdNotShareResource;
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

    public function getOpdSummaryByOpd($id_opd)
    {
        // 1. Query Anda sudah SEMPURNA. Tidak perlu diubah.
        $opd = Opd::select('id', 'nama_opd')
            ->with([
                'pegawai' => function ($query) {
                    $query->doesntHave('share_beritas')
                        ->select('id', 'nama', 'nip', 'opd_id');
                }
            ])
            ->find($id_opd);

        if (!$opd) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => 'Data OPD tidak ditemukan',
                'data' => null,
            ], 404);
        }

        // 2. INI PERBAIKANNYA:
        // Gunakan 'new OpdSummaryResource' untuk memformat satu objek $opd
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Data pegawai yang tidak memiliki share berita berhasil diambil',
            'data' => new OpdSummaryResource($opd), // <-- DIUBAH DI SINI
        ]);
    }
}
