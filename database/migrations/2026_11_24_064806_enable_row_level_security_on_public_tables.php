<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Supabase exposes the public schema through its Data API. CariPrint never uses it, so RLS is
 * enabled without policies to block that route. Laravel connects as the owner role and is unaffected.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->setRowLevelSecurity('ENABLE');
    }

    public function down(): void
    {
        $this->setRowLevelSecurity('DISABLE');
    }

    private function setRowLevelSecurity(string $action): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        foreach (Schema::getTables('public') as $table) {
            DB::statement(sprintf('ALTER TABLE "public"."%s" %s ROW LEVEL SECURITY', $table['name'], $action));
        }
    }
};
