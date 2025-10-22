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
        Schema::create('compensations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->timestamp('date'); // June 9, 2025 at 9:09:02 PM UTC+7
            $table->json('items'); // Array of items with cost, info, stt
            $table->json('violation_terms'); // Array of violation terms
            $table->decimal('total_amount', 12, 2); // 250000
            $table->timestamps();

            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compensations');
    }
};
