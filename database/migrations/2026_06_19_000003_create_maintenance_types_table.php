<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_category_id')->constrained('maintenance_categories')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('description', 255)->nullable();
            $table->unsignedInteger('recommended_interval_km')->nullable();
            $table->unsignedTinyInteger('recommended_interval_months')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['maintenance_category_id', 'active']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_types');
    }
};
