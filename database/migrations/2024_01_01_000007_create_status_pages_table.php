<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('color')->default('#10b981');
            $table->boolean('show_uptime')->default(true);
            $table->integer('history_days')->default(30);
            $table->timestamps();
        });
        
        Schema::create('status_page_monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_page_id')->constrained()->onDelete('cascade');
            $table->foreignId('monitor_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        
        Schema::create('monitor_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_page_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        
        Schema::create('monitor_group_monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitor_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('monitor_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        
        Schema::create('status_page_subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_page_id')->constrained()->onDelete('cascade');
            $table->string('email')->nullable();
            $table->string('webhook_url')->nullable();
            $table->enum('type', ['email', 'rss', 'webhook']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_page_subscribers');
        Schema::dropIfExists('monitor_group_monitors');
        Schema::dropIfExists('monitor_groups');
        Schema::dropIfExists('status_page_monitors');
        Schema::dropIfExists('status_pages');
    }
};

