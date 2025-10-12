<?php

namespace App\Http\Requests\ShareBerita;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShareBeritaRequestStore extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'berita_id' => 'required',
            'berita_title' => [
                'required',
                'string',
                Rule::unique('share_beritas', 'berita_title')
                    ->where(function ($query) {
                        return $query->where('platform', $this->platform);
                    }),
            ],
            'platform' => 'required|string',
            'url_berita' => 'required|string',
            'tanggal_share' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'pegawai_id.required' => 'Pegawai ID harus diisi',
            'pegawai_id.exists' => 'Pegawai ID tidak ditemukan',
            'berita_id.required' => 'Berita ID harus diisi',
            'berita_title.required' => 'Berita Title harus diisi',
            'berita_title.unique' => 'Berita ini sudah di-share di platform tersebut',
            'platform.required' => 'Platform harus diisi',
            'url_berita.required' => 'URL Berita harus diisi',
            'tanggal_share.required' => 'Tanggal Share harus diisi',
            'tanggal_share.date' => 'Tanggal Share harus berupa tanggal',
        ];
    }
}
