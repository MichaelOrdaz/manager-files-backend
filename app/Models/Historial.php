<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    use HasFactory;

    protected $table = 'historial';

    protected $hidden = [
        'deleted_at',
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function accion()
    {
        return $this->belongsTo(AccionHistorial::class, 'accion_id', 'id');
    }
}
