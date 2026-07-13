<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteers', function (Blueprint $table) {
            $table->id();

            // user who becomes volunteer
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // event for which volunteer applies
            $table->foreignId('event_id')
                ->constrained('events')
                ->cascadeOnDelete();

            // application status
            $table->string('status')->default('pending');
            // pending | approved | rejected

            // optional role inside event
            $table->string('role')->default('general');

            $table->timestamp('applied_at')->useCurrent();

            $table->timestamps();

            // prevent duplicate applications
            $table->unique(['user_id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteers');
    }
};