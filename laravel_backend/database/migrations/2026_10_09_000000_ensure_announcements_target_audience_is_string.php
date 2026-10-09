<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Some databases were imported with `target_audience` still an ENUM although the
     * earlier migration is marked as run. Per-user ("user_42") and "ADMIN" audiences then
     * fail to insert, so no dashboard notification is ever stored. Force it to a string.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql' || ! Schema::hasTable('announcements')) {
            return;
        }

        $column = DB::selectOne("SHOW COLUMNS FROM `announcements` WHERE Field = 'target_audience'");
        if ($column && stripos($column->Type, 'enum') !== false) {
            DB::statement("ALTER TABLE `announcements` MODIFY `target_audience` VARCHAR(255) NOT NULL DEFAULT 'all'");
        }
    }

    public function down(): void
    {
        //
    }
};
