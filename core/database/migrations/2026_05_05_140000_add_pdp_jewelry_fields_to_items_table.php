<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'complete_the_look_ids')) {
                $table->string('complete_the_look_ids', 512)->nullable();
            }
            if (! Schema::hasColumn('items', 'pdp_metal_variants')) {
                $table->json('pdp_metal_variants')->nullable();
            }
            if (! Schema::hasColumn('items', 'pdp_ar_model_url')) {
                $table->string('pdp_ar_model_url', 512)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            foreach (['complete_the_look_ids', 'pdp_metal_variants', 'pdp_ar_model_url'] as $col) {
                if (Schema::hasColumn('items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
