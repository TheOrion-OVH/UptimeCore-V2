<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->enum('impact', ['minor', 'major', 'critical']);
            $table->enum('status', ['investigating', 'identified', 'monitoring', 'resolved'])->default('investigating');
            $table->timestamp('started_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'started_at']);
        });
        
        Schema::create('incident_monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->onDelete('cascade');
            $table->foreignId('monitor_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
        
        Schema::create('incident_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['investigating', 'identified', 'monitoring', 'resolved']);
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_updates');
        Schema::dropIfExists('incident_monitors');
        Schema::dropIfExists('incidents');
    }
};

