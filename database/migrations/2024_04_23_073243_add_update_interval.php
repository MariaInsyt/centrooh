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
        Schema::table('billboards', function (Blueprint $table) {
            //
            $table->enum('update_interval', ['daily', 'weekly', 'biweekly', 'monthly', 'bimonthly', 'quarterly'])->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billboards', function (Blueprint $table) {
            //
        });
    }
};
