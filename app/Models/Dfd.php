<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dfd extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dfds';

    protected $fillable = [
        'dfd_number',
        'workplace_id',
        'owner_professional_id',
        'validate_professional_id',
        'back_to_owner',
        'is_archived',
        'is_valid',
        'object_type',
        'demand_description',
        'procurement_method',
        'price_registration_identification',
        'price_registration_originating_notice',
        'price_registration_publication_date',
        'price_registration_expiration_date',
        'preliminary_technical_study_required',
        'preliminary_technical_study_justification',
        'need_justification',
        'quantity_estimation_basis',
        'estimated_value',
        'budget_project_activity',
        'budget_expense_element',
        'budget_funding_source',
        'target_procurement_date',
        'strategic_planning_alignment',
        'priority_level',
    ];

    protected $casts = [
        'price_registration_publication_date' => 'date',
        'price_registration_expiration_date' => 'date',
        'preliminary_technical_study_required' => 'boolean',
        'estimated_value' => 'decimal:2',
    ];

    /**
     * Setor/Unidade onde a demanda foi criada.
     */
    public function workplace(): BelongsTo
    {
        return $this->belongsTo(Workplace::class);
    }

    /**
     * Usuário responsável principal pela demanda.
     */
    public function ownerProfessional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'owner_professional_id');
    }

    /**
     * Usuário responsável principal pela validação demanda.
     */
    public function validateProfessional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'validate_professional_id');
    }

    /**
     * Itens associados ao DFD.
     */
    public function items(): HasMany
    {
        return $this->hasMany(DfdItem::class, 'dfd_id');
    }

    /**
     * Membros da equipe de planejamento/aprovação do DFD.
     */
    public function members(): HasMany
    {
        return $this->hasMany(DfdMember::class, 'dfd_id');
    }
}