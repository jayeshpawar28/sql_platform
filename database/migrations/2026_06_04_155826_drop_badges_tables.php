<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversing
    }
};
