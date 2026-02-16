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
        Schema::create('quotation_info', function (Blueprint $table) {
        $table->id();
        $table->string('customer_id');
        $table->string('document_number');
        $table->date('date');
        $table->string('collective_no');
        $table->string('vendorNo');
        $table->string('email')->nullable();
        $table->date('closing_date');
        $table->string('closing_time')->nullable();
        $table->string('prepared_by');
        $table->string('approved_by')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_info');
    }
};
