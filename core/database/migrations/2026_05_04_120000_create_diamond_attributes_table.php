<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('diamond_attributes')) {
            return;
        }

        Schema::create('diamond_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->decimal('carat_weight', 8, 3)->nullable();
            $table->string('cut_grade', 32)->nullable();
            $table->string('color_grade', 8)->nullable();
            $table->string('clarity_grade', 8)->nullable();
            $table->string('shape', 32)->nullable();
            $table->decimal('table_pct', 5, 2)->nullable();
            $table->decimal('depth_pct', 5, 2)->nullable();
            $table->decimal('length_mm', 8, 3)->nullable();
            $table->decimal('width_mm', 8, 3)->nullable();
            $table->decimal('depth_mm', 8, 3)->nullable();
            $table->string('lab', 32)->nullable();
            $table->string('certificate_number', 64)->nullable();
            $table->string('certificate_url', 2048)->nullable();
            $table->boolean('is_lab_grown')->default(false);
            $table->string('fluorescence', 32)->nullable();
            $table->string('polish', 32)->nullable();
            $table->string('symmetry', 32)->nullable();
            $table->string('video_360_url', 2048)->nullable();
            $table->json('images_360')->nullable();
            $table->timestamps();

            $table->unique('item_id');
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diamond_attributes');
    }
};
