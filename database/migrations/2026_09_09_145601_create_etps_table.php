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
        Schema::create('etps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workstation_id');
            $table->foreignId('owner_professional_id');
            $table->foreignId('validate_professional_id')->nullable();
            $table->boolean('is_owner_bookmark')->default(false);
            $table->boolean('is_validate_bookmark')->default(false);
            $table->string('back_to_owner')->nullable();
            $table->string('process');
            $table->string('organ');
            $table->string('budget_unit');
            $table->boolean('is_valid')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etps');
    }
};
