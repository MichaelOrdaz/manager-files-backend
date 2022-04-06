<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDeDocumento extends Model
{
    use HasFactory;

    protected $table = 'tipos_de_documentos';

    protected $fillable = [
        'nombre',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'tipo_id', 'id');
    }
}
