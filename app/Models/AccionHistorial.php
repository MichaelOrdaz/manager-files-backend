<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccionHistorial extends Model
{
    use HasFactory;

    protected $table = 'acciones_historial';

    protected $fillable = [
        'nombre',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function historiales()
    {
        return $this->hasMany(Historial::class, 'accion_id', 'id');
    }
}
