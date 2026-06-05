<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'metal_type')) {
                $table->string('metal_type', 255)->nullable();
            }
            if (! Schema::hasColumn('items', 'gold_karat')) {
                $table->string('gold_karat', 255)->nullable();
            }
            if (! Schema::hasColumn('items', 'diamond_color')) {
                $table->string('diamond_color', 255)->nullable();
            }
            if (! Schema::hasColumn('items', 'diamond_quality')) {
                $table->string('diamond_quality', 255)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            foreach (['metal_type', 'gold_karat', 'diamond_color', 'diamond_quality'] as $col) {
                if (Schema::hasColumn('items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

