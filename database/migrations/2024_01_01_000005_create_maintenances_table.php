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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('notify')->default(true);
            $table->boolean('disable_alerts')->default(true);
            $table->timestamps();
            
            $table->index(['status', 'starts_at']);
        });
        
        Schema::create('maintenance_monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_id')->constrained()->onDelete('cascade');
            $table->foreignId('monitor_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_monitors');
        Schema::dropIfExists('maintenances');
    }
};

