<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('buildingName');            // Tên tòa nhà
            $table->string('address');                 // Địa chỉ
            $table->text('imageUrls')->nullable();     // Có thể là JSON list URL
            $table->decimal('latitude', 10, 7)->nullable();   // Vĩ độ
            $table->decimal('longitude', 10, 7)->nullable();  // Kinh độ
            $table->unsignedInteger('totalRooms')->default(0); // Số phòng
            $table->unsignedBigInteger('managerId');  // Khóa ngoại (nếu có bảng users)


            $table->foreign('managerId')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
