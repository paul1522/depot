<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->date('date')->index();
            $table->integer('quantity');
            $table->string('description');

            $table->string('sbt_ttranno')->unique();
            $table->string('sbt_orgno')->nullable();

            $table->foreignId('item_location_id')->constrained();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
