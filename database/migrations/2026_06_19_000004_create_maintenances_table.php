<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('maintenance_type_id')->constrained('maintenance_types')->onDelete('restrict');
            $table->text('description')->nullable();
            $table->date('performed_at');
            $table->unsignedInteger('current_mileage');
            $table->decimal('cost', 10, 2);
            $table->string('workshop_name')->nullable();
            $table->string('invoice_number')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('next_maintenance_mileage')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'vehicle_id']);
            $table->index('maintenance_type_id');
            $table->index('performed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
