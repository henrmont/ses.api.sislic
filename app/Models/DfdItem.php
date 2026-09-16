<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DfdItem extends Model
{
    protected $table = 'dfd_itens';

    protected $fillable = [
        'dfd_id',
        'name',
        'amount',
    ];

    // Relationships
    public function dfd()
    {
        return $this->belongsTo(Dfd::class);
    }
}
