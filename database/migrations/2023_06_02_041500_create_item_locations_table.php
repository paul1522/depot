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
        Schema::create('item_locations', function (Blueprint $table) {
            $table->id();

            $table->integer('quantity')->index();

            $table->foreignId('item_id')->constrained();
            $table->foreignId('location_id')->constrained();
            $table->foreignId('condition_id')->constrained();

            $table->timestamps();

            $table->unique(['location_id', 'item_id', 'condition_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
