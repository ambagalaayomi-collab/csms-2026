<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('proposals', 'technical_report_id')) {
            Schema::table('proposals', function (Blueprint $table) {
                $table->unsignedBigInteger('technical_report_id')->nullable()->after('project_request_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('proposals', 'technical_report_id')) {
            Schema::table('proposals', fn (Blueprint $table) => $table->dropColumn('technical_report_id'));
        }
    }
};
