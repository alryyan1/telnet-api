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
        Schema::create('device_logs', function (Blueprint $table) {
            $table->id();
            $table->string('severity'); // error, warning, info, debug
            $table->bigInteger('timestamp');
            $table->string('hostname');
            $table->string('application');
            $table->integer('device_id');
            $table->integer('event_id')->nullable()->default(0);
            $table->text('message');
            $table->json('context')->nullable(); // Store context as JSON
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('device_id');
            $table->index('severity');
            $table->index('timestamp');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_logs');
    }
};
