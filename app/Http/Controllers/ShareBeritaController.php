<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShareBerita\ShareBeritaRequest;
use App\Http\Requests\ShareBerita\ShareBeritaRequestStore;
use App\Http\Resources\ShareBeritaResource;
use App\Models\ShareBerita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShareBeritaController extends Controller
{
    public function store(ShareBeritaRequestStore $request)
    {
        Log::info($request->all());

        try {
            $shareBerita = ShareBerita::create($request->validated());
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Share Berita berhasil disimpan',
                'data' => $shareBerita,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Share Berita gagal disimpan',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function index($id)
    {
        $shareBeritas = ShareBerita::where('pegawai_id', $id)->get();
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Share Berita berhasil diambil',
            'data' => ShareBeritaResource::collection($shareBeritas),
        ]);
    }
}
