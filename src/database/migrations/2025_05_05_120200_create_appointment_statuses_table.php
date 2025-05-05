<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::create('appointment_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        DB::table('appointment_statuses')->insert([
            ['id' => 1, 'name' => 'requested', 'description' => 'Appointment requested by user'],
            ['id' => 2, 'name' => 'pending assignment', 'description' => 'Awaiting doctor assignment'],
            ['id' => 3, 'name' => 'assigned', 'description' => 'Doctor assigned to appointment'],
            ['id' => 4, 'name' => 'completed', 'description' => 'Appointment completed'],
            ['id' => 5, 'name' => 'cancelled', 'description' => 'Appointment cancelled'],
        ]);
    }
};
