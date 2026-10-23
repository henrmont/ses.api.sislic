<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DfdMember extends Model
{
    use HasFactory;

    protected $table = 'dfd_members';

    protected $fillable = [
        'dfd_id',
        'professional_id',
        'role',
    ];

    /**
     * DFD ao qual o membro está vinculado.
     */
    public function dfd(): BelongsTo
    {
        return $this->belongsTo(Dfd::class, 'dfd_id');
    }

    /**
     * Usuário vinculado como membro.
     */
    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'professional_id');
    }
}