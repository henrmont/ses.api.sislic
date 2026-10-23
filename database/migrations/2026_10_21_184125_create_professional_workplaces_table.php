<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professional_workplaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id');
            $table->foreignId('workplace_id');
            $table->timestamps();

            // Evita duplicidade do mesmo vínculo
            $table->unique(['professional_id', 'workplace_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_workplaces');
    }
};