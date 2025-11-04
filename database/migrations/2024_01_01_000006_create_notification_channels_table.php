<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['email', 'discord', 'webhook']);
            $table->string('label');
            $table->json('config');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
        
        Schema::create('monitor_notification_channel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitor_id')->constrained()->onDelete('cascade');
            $table->foreignId('notification_channel_id')->constrained()->onDelete('cascade');
            $table->boolean('notify_on_down')->default(true);
            $table->boolean('notify_on_up')->default(false);
            $table->integer('delay_between_alerts')->default(300); // 5 minutes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitor_notification_channel');
        Schema::dropIfExists('notification_channels');
    }
};

