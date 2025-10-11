<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Pegawai extends Model
{
    protected $table = 'pegawais';
    protected $guarded = [];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }

            if (empty($model->user_id)) {
                $user = User::create([
                    'name' => $model->nama,
                    'email' => $model->nip . '@app.com',
                    'password' => Hash::make(env('DEFAULT_PASSWORD')),
                    'nip' => $model->nip,
                    'username' => $model->nip,
                ]);
                $user->assignRole('pegawai');

                $model->user_id = $user->id;
            }
        });

        static::deleted(function ($model) {
            if ($model->user_id) {
                $user = User::find($model->user_id);
                if ($user) {
                    $user->delete();
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }
    public function share_beritas()
    {
        return $this->hasMany(ShareBerita::class, 'pegawai_id');
    }
}
