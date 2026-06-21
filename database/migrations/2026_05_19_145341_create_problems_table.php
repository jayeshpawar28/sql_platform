<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problems', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->string('topic')->index();
            $table->text('default_schema');
            $table->text('expected_output'); // JSON encoded expected query result
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problems');
    }
};
