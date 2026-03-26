<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email', 150)->index();
            $table->string('ip_address', 45)->index();
            $table->string('user_agent', 500)->nullable();
            $table->dateTime('attempt_time')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
