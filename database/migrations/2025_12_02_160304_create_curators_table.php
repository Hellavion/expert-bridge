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
        Schema::create('curators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('telegram_login')->nullable();
            $table->text('curator_bonus')->nullable();
            $table->foreignId('expert_id')->constrained('experts')->onDelete('cascade');
            $table->text('comment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curators');
    }
};
