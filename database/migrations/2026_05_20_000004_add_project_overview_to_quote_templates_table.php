<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_templates', function (Blueprint $table) {
            $table->text('project_overview')->nullable()->after('subtitle');
        });
    }

    public function down(): void
    {
        Schema::table('quote_templates', function (Blueprint $table) {
            $table->dropColumn('project_overview');
        });
    }
};
