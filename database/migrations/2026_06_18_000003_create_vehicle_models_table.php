<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->string('name');
            $table->string('fipe_code', 50)->nullable();
            $table->timestamps();

            $table->index(['brand_id', 'name']);
            $table->unique(['brand_id', 'fipe_code'])->whereNotNull('fipe_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_models');
    }
};
