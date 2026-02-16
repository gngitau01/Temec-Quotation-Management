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
        $table->foreignId('quotation_id')->constrained('quotation_info')->onDelete('cascade');
        $table->integer('item_no');
        $table->text('description');
        $table->integer('quantity');
        $table->string('unit');
        $table->decimal('unit_price', 10, 2)->nullable();
        $table->decimal('discount', 10, 2)->nullable();
        $table->decimal('vat', 10, 2)->nullable();
        $table->decimal('total_cost', 10, 2)->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
