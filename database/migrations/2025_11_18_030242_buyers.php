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
    Schema::create('buyers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('quotation_id')->constrained('quotation_info')->onDelete('cascade');
        $table->string('name');
        $table->string('address');
        $table->string('town');
        $table->string('country');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyers');
    }
};
