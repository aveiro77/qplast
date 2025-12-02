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
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'paid_amount')) {
                $table->decimal('paid_amount', 15, 2)->default(0)->after('total_price');
            }
            if (! Schema::hasColumn('sales', 'change_amount')) {
                $table->decimal('change_amount', 15, 2)->default(0)->after('paid_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'change_amount')) {
                $table->dropColumn('change_amount');
            }
            if (Schema::hasColumn('sales', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
        });
    }
};
