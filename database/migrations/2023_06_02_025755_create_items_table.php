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
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            $table->string('key')->index();
            $table->string('supplier_key')->index()->nullable();
            $table->string('description')->index();
            $table->string('group')->index()->nullable();
            $table->string('manufacturer')->index()->nullable();
            $table->string('sbt_item')->unique();
            $table->string('image_path')->nullable();
            $table->string('image_name')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_locations', function (Blueprint $table) {
            $table->dropForeign('item_locations_item_id_foreign');
        });
        Schema::dropIfExists('items');
    }
};
