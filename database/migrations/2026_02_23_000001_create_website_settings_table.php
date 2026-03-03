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
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('time_zone')->nullable();
            $table->string('currency', 8)->nullable();
            $table->decimal('vat_percentage', 5, 2)->nullable()->default(16);
            $table->text('external_api_upload_url')->nullable();
            $table->text('external_api_create_quotation_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};

