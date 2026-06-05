<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->where('id', 1)->update([
            'title' => 'African Carat',
            'home_page_title' => 'African Carat',
        ]);
    }

    public function down(): void
    {
        // Previous values vary per install; restore only if you know the prior title.
    }
};
