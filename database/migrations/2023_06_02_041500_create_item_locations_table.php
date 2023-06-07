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

            $table->string('location_id')->index();
            $table->string('item_id')->index();
            $table->integer('quantity')->index();

            $table->timestamps();

            $table->unique(['location_id', 'item_id']);
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
