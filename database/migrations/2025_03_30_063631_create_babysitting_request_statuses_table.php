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
        Schema::create('babysitting_request_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->string('color');
            $table->timestamps();
        });

        // Seed the initial data
        $statuses = [
            ['name' => 'pending', 'label' => 'Pending', 'color' => 'yellow'],
            ['name' => 'assigned', 'label' => 'Assigned', 'color' => 'purple'],
            ['name' => 'confirmed', 'label' => 'Confirmed', 'color' => 'blue'],
            ['name' => 'completed', 'label' => 'Completed', 'color' => 'green'],
            ['name' => 'cancelled', 'label' => 'Cancelled', 'color' => 'red'],
            ['name' => 'no_show', 'label' => 'No Show', 'color' => 'gray'],
        ];

        DB::table('babysitting_request_statuses')->insert($statuses);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('babysitting_request_statuses');
    }
};
