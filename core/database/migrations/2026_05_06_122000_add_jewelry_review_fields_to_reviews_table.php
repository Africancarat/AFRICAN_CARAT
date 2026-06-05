<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reviews')) {
            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('reviews', 'is_verified_purchase')) {
                $table->boolean('is_verified_purchase')->default(false)->after('status');
            }
            if (! Schema::hasColumn('reviews', 'occasion')) {
                $table->string('occasion', 64)->nullable()->after('is_verified_purchase');
            }
            if (! Schema::hasColumn('reviews', 'ring_size_ordered')) {
                $table->string('ring_size_ordered', 48)->nullable()->after('occasion');
            }
            if (! Schema::hasColumn('reviews', 'metal_type_ordered')) {
                $table->string('metal_type_ordered', 80)->nullable()->after('ring_size_ordered');
            }
            if (! Schema::hasColumn('reviews', 'review_photo')) {
                $table->string('review_photo', 255)->nullable()->after('metal_type_ordered');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('reviews')) {
            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            foreach (['review_photo', 'metal_type_ordered', 'ring_size_ordered', 'occasion', 'is_verified_purchase'] as $col) {
                if (Schema::hasColumn('reviews', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
