<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heartbeats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitor_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['up', 'down']);
            $table->integer('response_time')->nullable();
            $table->integer('status_code')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('checked_at');
            
            $table->index(['monitor_id', 'checked_at']);
            $table->index('checked_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heartbeats');
    }
};

