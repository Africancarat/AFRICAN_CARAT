<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'gold_weight')) {
                $table->decimal('gold_weight', 12, 3)->default(0)->after('gold_karat');
            }
            if (! Schema::hasColumn('items', 'labour_per_gram')) {
                $table->decimal('labour_per_gram', 12, 2)->default(0)->after('gold_weight');
            }
            if (! Schema::hasColumn('items', 'igi_per_carat')) {
                $table->decimal('igi_per_carat', 12, 2)->default(0)->after('labour_per_gram');
            }
            if (! Schema::hasColumn('items', 'margin_type')) {
                $table->string('margin_type', 32)->default('percent')->after('igi_per_carat');
            }
            if (! Schema::hasColumn('items', 'margin_value')) {
                $table->decimal('margin_value', 12, 2)->default(0)->after('margin_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            foreach (['gold_weight', 'labour_per_gram', 'igi_per_carat', 'margin_type', 'margin_value'] as $col) {
                if (Schema::hasColumn('items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
