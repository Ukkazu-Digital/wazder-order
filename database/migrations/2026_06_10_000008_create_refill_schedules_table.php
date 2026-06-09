<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refill_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tank_id')->constrained()->onDelete('cascade');
            $table->date('scheduled_date');
            $table->decimal('target_volume', 10, 2)->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refill_schedules');
    }
};
