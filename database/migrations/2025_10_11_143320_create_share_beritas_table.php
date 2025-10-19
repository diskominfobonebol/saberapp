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
        Schema::create('share_beritas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('berita_id');
            $table->string('berita_title');
            $table->enum('platform', ['whatsapp', 'facebook', 'instagram', 'twitter', 'email', 'telegram', 'x'])->default('whatsapp');
            $table->string('url_berita');
            $table->date('tanggal_share');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_beritas');
    }
};
