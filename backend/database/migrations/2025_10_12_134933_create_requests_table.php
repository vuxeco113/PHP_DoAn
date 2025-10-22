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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_khach_id'); // ID người dùng khách
            $table->unsignedBigInteger('room_id'); // ID phòng
            $table->string('loai_request')->default('thue_phong'); // Loại request
            $table->string('name'); // Tên người request
            $table->string('sdt'); // Số điện thoại
            $table->text('mo_ta')->nullable(); // Mô tả
            $table->string('status')->default('pending'); // Trạng thái
            $table->timestamp('thoi_gian'); // Thời gian tạo request
            $table->timestamps();

            // Foreign keys
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('user_khach_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
