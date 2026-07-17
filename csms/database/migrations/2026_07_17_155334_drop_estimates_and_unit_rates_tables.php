<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('estimates');
        Schema::dropIfExists('unit_rates');
    }

    public function down(): void
    {
        // Removed legacy tables are not recreated.
    }
};