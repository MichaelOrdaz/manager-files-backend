<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamentos';

    protected $fillable = [
        'nombre',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'departamento_id', 'id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'departamento_id', 'id');
    }
}
