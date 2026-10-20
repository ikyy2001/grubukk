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
        Schema::create('travel_policies', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kebijakan');
            $table->string('tingkat_jabatan'); // e.g. Eselon II, Eselon III, Pelaksana / Staf
            $table->decimal('max_hotel_per_malam', 15, 2)->default(0);
            $table->decimal('max_transport', 15, 2)->default(0);
            $table->decimal('uang_saku_harian', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_policies');
    }
};
