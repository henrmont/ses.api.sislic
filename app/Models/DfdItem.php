<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DfdItem extends Model
{
    use HasFactory;

    protected $table = 'dfd_items';

    protected $fillable = [
        'dfd_id',
        'item_number',
        'description',
        'supply_unit',
        'quantity',
    ];

    protected $casts = [
        'item_number' => 'integer',
        'quantity' => 'integer',
    ];

    /**
     * DFD ao qual o item pertence.
     */
    public function dfd(): BelongsTo
    {
        return $this->belongsTo(Dfd::class, 'dfd_id');
    }
}