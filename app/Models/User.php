<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftCascadeTrait, SoftDeletes;


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'nombre',
        'paterno',
        'materno',
        'celular',
        'imagen',
        'password',
        'departamento_id',
        'remember_token',
        'email_verified_at',
    ];

    protected $softCascade = [

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'departamento_id', 'id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'creador_id', 'id');
    }

    public function historias()
    {
        return $this->hasMany(Historial::class, 'user_id', 'id');
    }

    public function compartidos()
    {
        return $this->belongsToMany(Documento::class, 'documento_usuario', 'user_id', 'documento_id');
    }
}