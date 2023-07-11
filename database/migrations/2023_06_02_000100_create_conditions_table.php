<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conditions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('show_in_catalog')->default(true);
            $table->char('sbt_suffix', 1)->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('item_locations', function (Blueprint $table) {
            $table->dropForeign('item_locations_condition_id_foreign');
        });
        Schema::dropIfExists('conditions');
    }
};
