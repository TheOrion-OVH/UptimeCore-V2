<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['http', 'ping', 'tcp', 'dns', 'ssl']);
            $table->enum('status', ['pending', 'up', 'down', 'paused'])->default('pending');
            
            // Configuration HTTP
            $table->string('url')->nullable();
            $table->string('method')->default('GET');
            $table->json('headers')->nullable();
            $table->text('body')->nullable();
            $table->integer('expected_status_code')->default(200);
            $table->boolean('follow_redirects')->default(true);
            
            // Configuration Ping
            $table->string('host')->nullable();
            $table->integer('packet_count')->default(4);
            
            // Configuration TCP
            $table->integer('port')->nullable();
            
            // Configuration DNS
            $table->string('domain')->nullable();
            $table->string('record_type')->default('A');
            $table->string('expected_value')->nullable();
            
            // Configuration SSL
            $table->integer('days_before_alert')->default(15);
            
            // Paramètres généraux
            $table->integer('interval')->default(60);
            $table->integer('timeout')->default(10);
            $table->integer('retries')->default(3);
            $table->string('group')->nullable();
            
            // Statistiques
            $table->integer('response_time')->nullable();
            $table->decimal('uptime_percentage', 5, 2)->default(100);
            $table->integer('total_checks')->default(0);
            $table->integer('successful_checks')->default(0);
            $table->integer('failed_checks')->default(0);
            
            $table->timestamp('last_check_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};

