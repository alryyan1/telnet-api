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
        Schema::create('reboot_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('device_id');
            $table->string('requested_by')->nullable();
            $table->boolean('executed')->default(false);
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('device_id');
            $table->index(['device_id', 'executed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reboot_requests');
    }
};
