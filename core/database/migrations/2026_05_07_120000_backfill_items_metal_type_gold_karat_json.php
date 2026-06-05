<?php

use App\Models\Item;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Legacy rows may store metal_type / gold_karat as comma-separated text.
 * After switching to JSON in a TEXT column + Eloquent array cast, normalize existing CSV to JSON arrays.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('items')) {
            return;
        }

        foreach (['metal_type', 'gold_karat'] as $col) {
            if (! Schema::hasColumn('items', $col)) {
                return;
            }
        }

        DB::table('items')->orderBy('id')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                $upd = [];
                foreach (['metal_type', 'gold_karat'] as $col) {
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
                    DB::table('items')->where('id', $row->id)->update($upd);
                }
            }
        });
    }

    public function down(): void
    {
        //
    }
};
