<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('diamond_attributes')) {
            return;
        }

        Schema::table('diamond_attributes', function (Blueprint $table) {
            if (! Schema::hasColumn('diamond_attributes', 'certificate_report_image')) {
                $table->string('certificate_report_image', 512)->nullable()->after('certificate_url');
            }
            if (! Schema::hasColumn('diamond_attributes', 'certificate_report_pdf')) {
                $table->string('certificate_report_pdf', 512)->nullable()->after('certificate_report_image');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('diamond_attributes')) {
            return;
        }

        Schema::table('diamond_attributes', function (Blueprint $table) {
            if (Schema::hasColumn('diamond_attributes', 'certificate_report_pdf')) {
                $table->dropColumn('certificate_report_pdf');
            }
            if (Schema::hasColumn('diamond_attributes', 'certificate_report_image')) {
                $table->dropColumn('certificate_report_image');
            }
        });
    }
};
