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
        Schema::create('loan_history', function (Blueprint $table) {
            // $table->id();
            // $table->timestamps();
            $table->uuid('uuid')->primary();
            $table->unsignedBigInteger('user_id');
            $table->foreignUuid('book_uuid');
            // $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreignUuid('book_uuid')->references('uuid')->on('books')->onDelete('cascade');
            $table->date('loan_date');
            $table->date('return_date');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('book_uuid')->references('uuid')->on('books')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_history');
    }
};
