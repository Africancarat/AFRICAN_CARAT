<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_cutomizes', function (Blueprint $table) {
            if (! Schema::hasColumn('home_cutomizes', 't3_special_pick_intro')) {
                $table->longText('t3_special_pick_intro')->nullable()->after('hero_banner');
            }
        });
    }

    public function down(): void
    {
        Schema::table('home_cutomizes', function (Blueprint $table) {
            if (Schema::hasColumn('home_cutomizes', 't3_special_pick_intro')) {
                $table->dropColumn('t3_special_pick_intro');
            }
        });
    }
};
