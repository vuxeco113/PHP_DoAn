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
        //
       Schema::create('phieu_sua', function (Blueprint $table) {
            $table->id();
            $table->string('faultSource');
            $table->json('items');
            $table->date('ngaySua');
            $table->unsignedBigInteger('requestId');
            $table->unsignedBigInteger('roomId');
            $table->unsignedBigInteger('tenantId');
            $table->double('tongTien');
            $table->string('status')->default('pending');
            $table->foreign('requestId')->references('id')->on('requests')->onDelete('cascade');
            $table->foreign('roomId')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('tenantId')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('phieu_sua');
    }
};
