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
        Schema::table('sppd', function (Blueprint $table) {
            $table->string('status', 30)->default('pending_atasan')->after('acara'); // draft, pending_atasan, pending_keuangan, approved, rejected, completed
            $table->text('catatan_revisi')->nullable()->after('status');
            $table->string('file_undangan')->nullable()->after('catatan_revisi');
            $table->decimal('uang_muka', 15, 2)->default(0)->after('file_undangan');
            $table->timestamp('approved_by_atasan_at')->nullable()->after('uang_muka');
            $table->timestamp('approved_by_keuangan_at')->nullable()->after('approved_by_atasan_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'catatan_revisi',
                'file_undangan',
                'uang_muka',
                'approved_by_atasan_at',
                'approved_by_keuangan_at'
            ]);
        });
    }
};
