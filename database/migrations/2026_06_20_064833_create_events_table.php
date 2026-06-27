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
     Schema::create('events', function (Blueprint $table) {
    $table->id();

    $table->foreignId('club_id')
        ->constrained('clubs')
        ->cascadeOnDelete();

   $table->foreignId('venue_id')
    ->nullable()
    ->constrained('venues')
    ->nullOnDelete();

    $table->foreignId('created_by')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->string('title');
    $table->text('description')->nullable();

    $table->timestamp('start_time');
    $table->timestamp('end_time')->nullable();

    $table->string('status')->default('upcoming');

    $table->unsignedInteger('max_participants')->nullable();

    $table->timestamps();

    $table->index(['club_id', 'status']);
    $table->index('created_by');
});
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
