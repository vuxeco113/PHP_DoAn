<?php
// database/migrations/2025_01_01_000000_create_contracts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id'); // Liên kết với chủ trọ
            $table->unsignedBigInteger('tenant_id'); // Liên kết với khách thuê
            $table->unsignedBigInteger('room_id'); // Liên kết với phòng
            $table->bigInteger('deposit_amount'); // Số tiền đặt cọc
            $table->bigInteger('rent_amount'); // Số tiền thuê
            $table->dateTime('start_date'); // Ngày bắt đầu
            $table->dateTime('end_date'); // Ngày kết thúc
            $table->text('terms_and_conditions')->nullable(); // Điều khoản và điều kiện
            $table->enum('status', ['active', 'expired', 'terminated', 'pending'])->default('pending');
            $table->json('payment_history_ids')->nullable(); // Mảng ID lịch sử thanh toán
            $table->timestamps();
            // Indexes
            $table->index('owner_id');
            $table->index('tenant_id');
            $table->index('room_id');
            $table->index('status');
            $table->index(['start_date', 'end_date']);

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
         });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
