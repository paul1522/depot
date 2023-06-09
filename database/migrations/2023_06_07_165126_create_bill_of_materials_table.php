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
        Schema::create('bill_of_materials', function (Blueprint $table) {
            $table->id();

            $table->string('option_group')->index()->nullable();
            $table->integer('min_qty');
            $table->integer('max_qty');

            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('master_item_id');

            $table->foreign('master_item_id')
                ->references('id')
                ->on('items')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_of_materials');
    }
};
