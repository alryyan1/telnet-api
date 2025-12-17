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
        Schema::create('device_statuses', function (Blueprint $table) {
            $table->id();
            $table->integer('device_id');
            $table->integer('client_id')->default(0);
            $table->string('firmware_version');
            $table->string('ip_address');
            $table->bigInteger('timestamp');
            $table->decimal('gps_latitude', 10, 7);
            $table->decimal('gps_longitude', 10, 7);
            $table->decimal('gps_altitude', 10, 2)->nullable();
            $table->decimal('gps_accuracy', 10, 2)->nullable()->default(0);
            $table->integer('rssi')->nullable();
            $table->integer('batterie_level')->nullable();
            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('humidity', 5, 2)->nullable();
            $table->decimal('mean_vibration', 5, 2)->nullable();
            $table->decimal('light', 8, 2)->nullable();
            $table->string('status')->nullable();
            $table->integer('nbrfid')->nullable()->default(0);
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('device_id');
            $table->index('timestamp');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_statuses');
    }
};
