<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_cutomizes', function (Blueprint $table) {
            if (! Schema::hasColumn('home_cutomizes', 't3_split_path_banner')) {
                $table->longText('t3_split_path_banner')->nullable();
            }
        });

        Schema::table('extra_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('extra_settings', 'is_t3_split_path_banner')) {
                $table->unsignedTinyInteger('is_t3_split_path_banner')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('home_cutomizes', function (Blueprint $table) {
            if (Schema::hasColumn('home_cutomizes', 't3_split_path_banner')) {
                $table->dropColumn('t3_split_path_banner');
            }
        });

        Schema::table('extra_settings', function (Blueprint $table) {
            if (Schema::hasColumn('extra_settings', 'is_t3_split_path_banner')) {
                $table->dropColumn('is_t3_split_path_banner');
            }
        });
    }
};
