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
        Schema::table('items_prices', function (Blueprint $table) {
            $table->decimal('labour_per_gram', 10, 2)->nullable();
            $table->decimal('margin_value', 10, 2)->nullable();
            $table->integer('diamond_count')->nullable();
            $table->decimal('diamond_weight', 10, 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items_prices', function (Blueprint $table) {
            //
        });
    }
};
