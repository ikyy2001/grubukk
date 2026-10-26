<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fixes:
     * 1. Menambahkan penanda `uang_muka_ditetapkan` supaya nilai uang muka
     *    pada reimbursement TIDAK lagi terisi diam-diam dengan salinan estimasi
     *    pengajuan (menyebabkan selisih palsu sebelum ada nota).
     * 2. Membersihkan duplikat reimbursement per SPPD dan memasang unique index.
     */
    public function up(): void
    {
        Schema::table('sppd_reimbursements', function (Blueprint $table) {
            $table->boolean('uang_muka_ditetapkan')->default(false)->after('uang_muka');
        });

        // 1. Satu SPPD hanya boleh punya satu data reimbursement.
        $duplicates = DB::table('sppd_reimbursements')
            ->select('sppd_id', DB::raw('MIN(id) as keep_id'))
            ->groupBy('sppd_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $ids = DB::table('sppd_reimbursements')
                ->where('sppd_id', $duplicate->sppd_id)
                ->where('id', '<>', $duplicate->keep_id)
                ->pluck('id');

            DB::table('sppd_reimbursement_items')->whereIn('reimbursement_id', $ids)->delete();
            DB::table('sppd_reimbursements')->whereIn('id', $ids)->delete();
        }

        // 2. Nilai uang_muka lama hanya salinan estimasi pengajuan dan belum pernah
        //    dikonfirmasi, jadi dikosongkan agar tidak muncul "tiba-tiba" di halaman.
        DB::table('sppd_reimbursements')
            ->where('uang_muka_ditetapkan', false)
            ->update([
                'uang_muka' => 0,
                'selisih'   => DB::raw('total_pengeluaran_riil'),
            ]);

        Schema::table('sppd_reimbursements', function (Blueprint $table) {
            $table->unique('sppd_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppd_reimbursements', function (Blueprint $table) {
            $table->dropUnique(['sppd_id']);
            $table->dropColumn('uang_muka_ditetapkan');
        });
    }
};
