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
        Schema::create('device_configs', function (Blueprint $table) {
            $table->id();
            $table->integer('device_id')->unique(); // One config per device
            $table->integer('CAN_id')->nullable();
            $table->boolean('RFID_enable')->default(false);
            $table->boolean('SD_enable')->default(false);
            $table->integer('client_id')->nullable();
            $table->bigInteger('config_timestamp'); // Must increment on each update
            $table->string('configured_by')->nullable();
            $table->boolean('debug_enable')->default(false);
            $table->string('endpoint_URL')->nullable();
            $table->bigInteger('frequency')->nullable();
            $table->integer('mode')->nullable();
            $table->integer('sf')->nullable();
            $table->boolean('status')->default(true);
            $table->json('thresholds')->nullable(); // Store thresholds as JSON
            $table->bigInteger('timestamp')->nullable();
            $table->integer('txp')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('device_id');
            $table->index('config_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_configs');
    }
};
