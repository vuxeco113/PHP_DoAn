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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('khachThueId');
            $table->unsignedBigInteger('ownerId');
            $table->unsignedBigInteger('roomId');
            $table->timestamp('date');
            $table->dateTime('paidAt')->nullable(); 
            $table->bigInteger('priceDien');
            $table->bigInteger('priceRoom');
            $table->bigInteger('priceWater');
            $table->bigInteger('soNguoi');
            $table->bigInteger('sodienCu');
            $table->bigInteger('sodienMoi');
            $table->bigInteger('amenitiesPrice');
            $table->string('status')->default('pending');
            $table->bigInteger('sumPrice');
            $table->string('thangNam', 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
