<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First truncate to avoid foreign key constraints errors with existing invalid data
        DB::table('stock_ins')->truncate();
        DB::table('stock_outs')->truncate();

        Schema::table('stock_ins', function (Blueprint $table) {
            $table->dropColumn('item_name');
            $table->dropColumn('date');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
        });

        Schema::table('stock_outs', function (Blueprint $table) {
            $table->dropColumn('item_name');
            $table->dropColumn('date');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            if (Schema::hasColumn('stock_ins', 'item_id')) {
                $table->dropForeign(['item_id']);
                $table->dropColumn('item_id');
            }
            if (!Schema::hasColumn('stock_ins', 'item_name')) {
                $table->string('item_name')->nullable();
            }
            if (!Schema::hasColumn('stock_ins', 'date')) {
                $table->date('date')->nullable();
            }
        });

        Schema::table('stock_outs', function (Blueprint $table) {
            if (Schema::hasColumn('stock_outs', 'item_id')) {
                $table->dropForeign(['item_id']);
                $table->dropColumn('item_id');
            }
            if (!Schema::hasColumn('stock_outs', 'item_name')) {
                $table->string('item_name')->nullable();
            }
            if (!Schema::hasColumn('stock_outs', 'date')) {
                $table->date('date')->nullable();
            }
        });
    }
};
