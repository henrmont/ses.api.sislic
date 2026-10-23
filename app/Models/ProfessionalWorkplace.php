<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalWorkplace extends Model
{
    use HasFactory;

    protected $fillable = [
        'professional_id',
        'workplace_id',
    ];
    
}