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
        Schema::table('stock_outs', function (Blueprint $table) {
            $table->string('status')->default('Consumed')->after('quantity'); // Damaged or Consumed
        });

        // Clear existing maintenance data to avoid foreign key issues
        DB::table('maintenances')->truncate();

        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropColumn('item_name');
            $table->foreignId('item_id')->after('id')->constrained('items')->onDelete('cascade');
            $table->integer('quantity')->default(1)->after('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_outs', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropColumn(['item_id', 'quantity']);
            $table->string('item_name')->after('id');
        });
    }
};
