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
        Schema::create('sppd_reimbursements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sppd_id');
            $table->decimal('total_pengeluaran_riil', 15, 2)->default(0);
            $table->decimal('uang_muka', 15, 2)->default(0);
            $table->decimal('selisih', 15, 2)->default(0); // Positif: reimburse ke pegawai; Negatif: pegawai mengembalikan
            $table->string('status_pembayaran', 30)->default('pending'); // pending, verified, paid
            $table->text('catatan_keuangan')->nullable();
            $table->timestamps();

            $table->foreign('sppd_id')->references('id')->on('sppd')->onDelete('cascade');
        });

        Schema::create('sppd_reimbursement_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reimbursement_id');
            $table->string('kategori'); // Transport, Penginapan/Hotel, Uang Saku/Harian, Lain-lain
            $table->string('keterangan');
            $table->decimal('biaya', 15, 2);
            $table->string('file_nota')->nullable();
            $table->timestamps();

            $table->foreign('reimbursement_id')->references('id')->on('sppd_reimbursements')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sppd_reimbursement_items');
        Schema::dropIfExists('sppd_reimbursements');
    }
};
