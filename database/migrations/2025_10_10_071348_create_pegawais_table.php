<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('nip');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nama');
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('agama')->nullable();
            $table->string('status_perkawinan')->nullable();
            $table->string('nomor_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('jenis_pegawai')->nullable();
            $table->string('golongan')->nullable();
            $table->date('tmt_golongan')->nullable();
            $table->string('jenis_jabatan')->nullable();
            $table->string('jabatan')->nullable();
            $table->date('tmt_jabatan')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->integer('tahun_lulus')->nullable();
            $table->foreignId('opd_id')->nullable()->constrained('opds')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
