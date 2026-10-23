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
        Schema::create('dfd_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dfd_id');
            $table->foreignId('professional_id');
            $table->string('role')->nullable()->comment('Ex: Integrante Requisitante, Integrante Técnico, Aprovação/De Acordo');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dfd_members');
    }
};