<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('breed')->nullable();
            $table->string('registration_number')->unique();
            $table->foreignId('animal_type_id')->constrained('animal_types');
            $table->date('date_of_birth');
            $table->foreignId('owner_id')->constrained('users');
            $table->timestamps();
        });
    }
};