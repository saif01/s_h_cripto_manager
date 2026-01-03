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
        Schema::create('cpt_coin_prices', function (Blueprint $table) {
            $table->id();
            $table->string('asset_name');
            $table->string('asset_id')->nullable();
            $table->decimal('asset_quantity', 25, 6)->nullable();
            $table->decimal('average_asset_price', 25, 6)->nullable();
            $table->decimal('total_invested', 25, 6)->nullable();
            $table->decimal('current_price', 25, 6)->nullable();
            $table->decimal('current_asset_value', 25, 6)->nullable();
            $table->text('details')->nullable();
            $table->integer('status')->nullable()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpt_coin_prices');
    }
};
