<?php

use App\Models\Item;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Legacy rows may store color_grade / clarity_grade as single values or CSV.
 * After switching to JSON in a TEXT column + Eloquent array cast, normalize existing values to JSON arrays.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('diamond_attributes')) {
            return;
        }

        foreach (['color_grade', 'clarity_grade'] as $col) {
            if (! Schema::hasColumn('diamond_attributes', $col)) {
                return;
            }
        }

        DB::table('diamond_attributes')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                $upd = [];
                foreach (['color_grade', 'clarity_grade'] as $col) {
                    $raw = $row->{$col} ?? null;
                    if ($raw === null || $raw === '') {
                        continue;
                    }
                    $t = trim((string) $raw);
                    if ($t === '' || str_starts_with($t, '[')) {
                        continue;
                    }
                    $arr = Item::normalizeJewelryOptionList($t);
                    if ($arr !== null) {
                        $upd[$col] = json_encode($arr);
                    }
                }
                if ($upd !== []) {
                    DB::table('diamond_attributes')->where('id', $row->id)->update($upd);
                }
            }
        });
    }

    public function down(): void
    {
        //
    }
};

