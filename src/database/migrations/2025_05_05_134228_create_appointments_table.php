<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets');
            $table->foreignId('doctor_id')->nullable()->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->date('date');
            $table->string('time_of_day')->nullable();
            $table->foreignId('status_id')->constrained('appointment_statuses');
            $table->text('symptoms')->nullable();
            $table->timestamps();
        });
    }
};
