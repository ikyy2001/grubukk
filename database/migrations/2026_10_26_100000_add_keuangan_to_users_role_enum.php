<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('Admin', 'Kepala Bidang', 'Kepala Seksi', 'Staff', 'Keuangan') NOT NULL DEFAULT 'Staff'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('Admin', 'Kepala Bidang', 'Kepala Seksi', 'Staff') NOT NULL DEFAULT 'Staff'");
        }
    }
};
