<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Pegawai\PegawaiResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        // Validasi input dari LoginRequest
        $credentials = $request->validated();

        // Cari user berdasarkan NIP
        $user = User::where('nip', $credentials['nip'])->first();

        // Cek apakah user ada dan password cocok
        if (! $user) {
            return response()->json([
                'status' => false,
                'code' => 401,
                'message' => 'NIP tidak ditemukan.',
            ], 401);
        }

        // Hapus token lama agar tidak numpuk (opsional tapi disarankan)
        $user->tokens()->delete();

        // Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        // Response sukses
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Nip Berhasil Login',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ]);
    }

    public function user()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw ValidationException::withMessages([
                    "status" => false,
                    "code" => 404,
                    "message" => "User tidak ditemukan",
                ], 404);
            }

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'User berhasil diambil',
                'data' => new UserResource($user)
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'code' => 404,
                    'status' => false,
                    'message' => $e->getMessage()
                ],
                404
            );
        }
    }

    public function logout()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                throw ValidationException::withMessages([
                    "status" => false,
                    "code" => 404,
                    "message" => "User tidak ditemukan",
                ], 404);
            }
            $user->tokens()->delete();
        } catch (\Exception $e) {
            return response()->json([
                'code' => 404,
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Logout berhasil',
        ], 200);
    }
}
