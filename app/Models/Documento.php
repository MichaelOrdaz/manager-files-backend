<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $table = 'documentos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'ubicacion',
        'etiquetas',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'creador_id', 'id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoDeDocumento::class, 'tipo_id', 'id');
    }

    public function antecesor()
    {
        return $this->belongsTo(Documento::class, 'antecesor_id', 'id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id', 'id');
    }

    public function compartidos()
    {
        return $this->belongsToMany(User::class, 'documento_usuario', 'documento_id', 'user_id');
    }
}
