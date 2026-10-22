<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workplace extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'parent_id',
        'name',
        'budget_entity',
    ];

    /**
     * Relationships
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Workplace::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Workplace::class, 'parent_id');
    }
}