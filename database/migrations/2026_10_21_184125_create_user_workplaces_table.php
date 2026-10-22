<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_workplaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('workplace_id');
            $table->timestamps();

            // Evita duplicidade do mesmo vínculo
            $table->unique(['user_id', 'workplace_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_workplaces');
    }
};