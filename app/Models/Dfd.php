<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dfd extends Model
{
    protected $fillable = [
        'workstation_id',
        'owner_professional_id',
        'validate_professional_id',
        'is_owner_bookmark',
        'is_validate_bookmark',
        'back_to_owner',
        'name',
        'description',
        'justification',
        'is_valid',
        'is_archived',
        'etp_id'
    ];

    /**
     * Scope para filtrar DFDs pertencentes a QUALQUER uma das estações do usuário logado.
     */
    public function scopeByWorkstation(Builder $query): Builder
    {
        $user = auth()->user();
        $professional = $user?->professional;

        // Recupera a coleção/array de IDs de todas as estações do profissional
        // Supondo relação N:N ($professional->workstations)
        $workstationIds = $professional?->workstations->pluck('id')->toArray() ?? [];

        if (empty($workstationIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $q) use ($workstationIds) {
            $q->whereHas('ownerProfessional.workstations', function (Builder $ownerQuery) use ($workstationIds) {
                $ownerQuery->whereIn('workstations.id', $workstationIds);
            })
            ->orWhereHas('validateProfessional.workstations', function (Builder $validateQuery) use ($workstationIds) {
                $validateQuery->whereIn('workstations.id', $workstationIds);
            });
        });
    }

    // Relationships
    public function workstation(): BelongsTo
    {
        return $this->belongsTo(Workstation::class);
    }

    public function ownerProfessional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'owner_professional_id');
    }

    public function validateProfessional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'validate_professional_id');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(DfdItem::class);
    }

    public function etp(): BelongsTo
    {
        return $this->belongsTo(Etp::class);
    }

    // Accessors & Mutators
    protected $appends = [
        'owner',
        'owner_status',
        'validate',
        'validate_status',
    ];

    protected function owner(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->owner_professional_id == Professional::where('user_id', auth()->id())->first()->id ? true : false
        );
    }

    protected function ownerStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->itens()->exists() ? true : false
        );
    }

    protected function validate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->validate_professional_id == Professional::where('user_id', auth()->id())->first()->id ? true : false
        );
    }

    protected function validateStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_valid
        );
    }

}