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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('factory_number', 50)->unique();
            $table->string('name', 100);
            $table->string('address', 255)->nullable();
            $table->string('town', 100)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('contact_person', 100)->nullable();
            $table->timestamps();

            $table->index('factory_number');
            $table->index('name');
            $table->index('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer');
    }
};
