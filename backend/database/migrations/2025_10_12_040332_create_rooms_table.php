<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('area', 8, 2);
            $table->integer('capacity');
            $table->json('amenities')->nullable();
            $table->json('imageUrls')->nullable();
            $table->string('status')->default('available');
            $table->decimal('latitude', 10, 7)->default(0);
            $table->decimal('longitude', 10, 7)->default(0);
            $table->integer('sodien')->default(0);
            //$table->string('currentTenantId')->nullable();
            $table->unsignedBigInteger('ownerId')->nullable();
            //$table->date('rentStartDate')->nullable();
            $table->unsignedBigInteger('buildingId');
            $table->foreign('buildingId')->references('id')->on('buildings')->onDelete('cascade');
            $table->foreign('ownerId')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
