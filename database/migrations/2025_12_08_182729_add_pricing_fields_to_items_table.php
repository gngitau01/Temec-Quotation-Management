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
        Schema::table('items', function (Blueprint $table) {
            // Add pricing fields if they don't exist
            if (!Schema::hasColumn('items', 'unit_price')) {
                $table->decimal('unit_price', 12, 2)->nullable()->after('unit');
            }
            if (!Schema::hasColumn('items', 'discount')) {
                $table->decimal('discount', 12, 2)->nullable()->default(0)->after('unit_price');
            }
            if (!Schema::hasColumn('items', 'vat')) {
                $table->decimal('vat', 12, 2)->nullable()->default(0)->after('discount');
            }
            if (!Schema::hasColumn('items', 'vat_percentage')) {
                $table->decimal('vat_percentage', 5, 2)->default(16)->after('vat');
            }
            if (!Schema::hasColumn('items', 'total_cost')) {
                $table->decimal('total_cost', 12, 2)->nullable()->after('vat_percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'unit_price',
                'discount',
                'vat',
                'vat_percentage',
                'total_cost'
            ]);
        });
    }
};
