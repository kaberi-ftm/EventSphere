<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('clubs', function (Blueprint $table) {
    $table->id();

    $table->string('name', 100);
    $table->text('description')->nullable();
    $table->date('founded_date')->nullable();

    $table->foreignId('admin_user_id')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

    $table->timestamps();

    $table->index('admin_user_id');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};