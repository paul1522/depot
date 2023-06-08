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
        Schema::create('bill_of_materials_details', function (Blueprint $table) {
            $table->id();

            $table->string('option_group')->index()->nullable();
            $table->integer('min_qty');
            $table->integer('max_qty');

            $table->foreignId('item_id')->constrained();
            $table->foreignId('bill_of_materials_header_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_of_materials_details');
    }
};
