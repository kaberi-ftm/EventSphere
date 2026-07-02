<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $table) {

            $table->string('name', 100)->after('id');

            $table->string('location', 255)->after('name');

            $table->integer('capacity')->default(0)->after('location');

            $table->text('description')->nullable()->after('capacity');

        });
    }

    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {

            $table->dropColumn([
                'name',
                'location',
                'capacity',
                'description',
            ]);

        });
    }
};