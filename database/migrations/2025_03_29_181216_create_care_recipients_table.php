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
        Schema::create('care_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('babysitting_request_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('name')->nullable();
            $table->date('date_of_birth');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('care_recipients');
    }
};
