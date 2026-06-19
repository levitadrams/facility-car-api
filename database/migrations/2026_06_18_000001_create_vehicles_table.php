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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nickname')->nullable();
            $table->string('brand');
            $table->string('model');
            $table->integer('year');
            $table->string('plate');
            $table->string('color')->nullable();
            $table->string('fuel_type')->nullable();
            $table->integer('current_mileage')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'plate']);
            $table->index(['user_id', 'brand', 'model', 'plate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
