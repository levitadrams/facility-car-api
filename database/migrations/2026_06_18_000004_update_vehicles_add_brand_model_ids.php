<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'brand', 'model', 'plate']);
            $table->foreignId('brand_id')->nullable()->constrained('brands')->after('user_id');
            $table->foreignId('vehicle_model_id')->nullable()->constrained('vehicle_models')->after('brand_id');
            $table->dropColumn(['brand', 'model']);
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('brand')->after('nickname');
            $table->string('model')->after('brand');
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['vehicle_model_id']);
            $table->dropColumn(['brand_id', 'vehicle_model_id']);
        });
    }
};
