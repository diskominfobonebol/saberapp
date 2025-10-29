<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{


    public function getAllPegawaiShareBerita()
    {
        $pegawai = Pegawai::leftJoin('opds', 'pegawais.opd_id', '=', 'opds.id')
            ->leftJoin('share_beritas', 'pegawais.id', '=', 'share_beritas.pegawai_id')
            ->select(
                'pegawais.id',
                'pegawais.nama',
                'pegawais.nip',
                'opds.nama_opd',
                DB::raw('COUNT(share_beritas.id) as total_share')
            )
            ->groupBy('pegawais.id', 'pegawais.nama', 'pegawais.nip', 'opds.nama_opd')
            ->orderByDesc('total_share')
            ->get();

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Data pegawai dan jumlah share berita berhasil diambil',
            'data' => $pegawai,
        ]);
    }
}
