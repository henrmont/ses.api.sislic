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
        Schema::create('dfds', function (Blueprint $table) {
            $table->id();

            // Introductory Section
            $table->string('dfd_number')->comment('Ex: 001/2024/CMVF/SGP/GBSAAS/SES-MT');
            $table->foreignId('workplace_id');
            $table->foreignId('owner_professional_id')->comment('Usuário responsável pela demanda');
            $table->foreignId('validate_professional_id')->nullable()->comment('Usuário responsável pela validação');
            $table->string('back_to_owner')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_valid')->nullable();

            // 1. Object (Preliminary Solution)
            $table->string('object_type')->comment('Ex: material_consumo, servico_nao_continuado, etc.');
            $table->text('demand_description');

            // 2. Suggested Procurement Method
            $table->string('procurement_method')->comment('Ex: lei_14133_decreto_1525, dispensa_inexigibilidade, etc.');
            $table->string('price_registration_identification')->nullable();
            $table->string('price_registration_originating_notice')->nullable();
            $table->date('price_registration_publication_date')->nullable();
            $table->date('price_registration_expiration_date')->nullable();
            
            // Preliminary Technical Study (ETP)
            $table->boolean('preliminary_technical_study_required')->default(false);
            $table->text('preliminary_technical_study_justification')->nullable();

            // 3. Justification of Need
            $table->text('need_justification');
            $table->text('quantity_estimation_basis')->nullable();

            // 4. Estimated Value / Budgetary Forecast (PTA)
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->string('budget_project_activity')->nullable()->comment('Ex: 2751');
            $table->string('budget_expense_element')->nullable()->comment('Ex: 3.3.90.30.061');
            $table->string('budget_funding_source')->nullable()->comment('Ex: 1.500.1002');

            // 5 & 6. Deadlines and Strategic Alignment
            $table->string('target_procurement_date')->comment('Ex: NOVEMBRO/2025');
            $table->text('strategic_planning_alignment');

            // 7. Priority Level
            $table->text('priority_level');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dfds');
    }
};