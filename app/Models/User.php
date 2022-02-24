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
        'id',
        'email',
        'password',
        'firebase_uid',
        'tutor_id',
        'activo'
    ];

    protected $softCascade = [
        'actividades',
        'avisos',
        'conferencias',
        'datosAcademicos',
        'datosFamiliares',
        'datosGenerales',
        'encuestas',
        'encuestaPreguntas',
        'encuestaRespuestas',
        'examenes',
        'examenCalificaciones',
        'examenPreguntas',
        'examenRespuestas',
        'tareas',
        'tareaEnviadas',
        'tutorias',
        'alumnoMaterialDidactico',
        'alumnoGrupo',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
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

    protected $attributes = [
        'activo' => true,
    ];

    public function alumnoGrupo()
    {
        return $this->hasOne(AlumnoGrupo::class, 'usuario_id');
    }

    public function alumnoGrupoEnCurso()
    {
        return $this->hasOne(AlumnoGrupo::class, 'usuario_id')->latestOfMany();
    }

    public function especialidadPeriodoGrupo()
    {
        return $this->belongsToMany(EspecialidadPeriodoGrupo::class, 'alumnos_grupos', 'usuario_id', 'especialidad_periodo_grupo_id');
    }

    /**
     * Get the actividade for this model.
     *
     * @return App\Models\Actividad
     */
    public function actividades()
    {
        return $this->hasMany('App\Models\Actividad','usuario_id','id');
    }

    /**
     * Get the aviso for this model.
     *
     * @return App\Models\Aviso
     */
    public function avisos()
    {
        return $this->hasMany('App\Models\Aviso','usuario_id','id');
    }

    /**
     * Get the conferencia for this model.
     *
     * @return App\Models\Conferencia
     */
    public function conferencias()
    {
        return $this->hasMany('App\Models\Conferencia','usuario_id','id');
    }

    /**
     * Get the datosAcademico for this model.
     *
     * @return App\Models\DatosAcademicos
     */
    public function datosAcademicos()
    {
        return $this->hasOne('App\Models\DatosAcademicos','usuario_id','id')->with('Status');
    }

    /**
     * Get the datosFamiliare for this model.
     *
     * @return App\Models\DatosFamiliares
     */
    public function datosFamiliares()
    {
        return $this->hasMany('App\Models\DatosFamiliares','usuario_id','id');
    }

    /**
     * Get the DatosGenerales for this model.
     *
     * @return App\Models\DatosGenerales
     */
    public function datosGenerales()
    {
        return $this->hasOne('App\Models\DatosGenerales','usuario_id','id')->with('municipio','estado');
    }

    /**
     * Get the encuesta for this model.
     *
     * @return App\Models\Encuestum
     */
    public function encuestas()
    {
        return $this->hasMany('App\Models\Encuesta','usuario_id','id');
    }

    /**
     * Get the encuestasPregunta for this model.
     *
     * @return App\Models\EncuestaPregunta
     */
    public function encuestaPreguntas()
    {
        return $this->hasMany('App\Models\EncuestaPregunta','usuario_id','id');
    }

    /**
     * Get the encuestasRespuesta for this model.
     *
     * @return App\Models\EncuestaRespuesta
     */
    public function encuestaRespuestas()
    {
        return $this->hasMany('App\Models\EncuestaRespuesta','usuario_id','id');
    }

    public function examenes()
    {
        return $this->hasMany('App\Models\Examen','usuario_id','id');
    }

    /**
     * Get the examenesCalificacione for this model.
     *
     * @return App\Models\ExamenCalificacion
     */
    public function examenCalificaciones()
    {
        return $this->hasMany('App\Models\ExamenCalificacion','usuario_id','id')->with('status:id,nombre');
    }

    /**
     * Get the examenesPregunta for this model.
     *
     * @return App\Models\ExamenPregunta
     */
    public function examenPreguntas()
    {
        return $this->hasMany('App\Models\ExamenPregunta','usuario_id','id');
    }

    /**
     * Get the examenesRespuesta for this model.
     *
     * @return App\Models\ExamenRespuesta
     */
    public function examenRespuestas()
    {
        return $this->hasMany('App\Models\ExamenRespuesta','usuario_id','id');
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'usuario_id');
    }

    public function tareaEnviadas()
    {
        return $this->hasMany(TareaEnviada::class, 'usuario_id','id');
    }

    /**
     * Get the tutoria for this model.
     *
     * @return App\Models\Tutoria
     */
    public function tutorias()
    {
        return $this->hasMany('App\Models\Tutoria','usuario_id','id');
    }

    public function alumnoMaterialDidactico()
    {
        return $this->hasMany(AlumnoMaterialDidactico::class, 'usuario_id','id');
    }

    public function nombreCompleto()
    {
        return !$this->datosGenerales ? null : $this->datosGenerales->nombre .' '. $this->datosGenerales->apellido_paterno .' '. $this->datosGenerales->apellido_materno;
    }

    public function AsistenciaAlumno()
    {
        return $this->hasMany(AsistenciaAlumno::class, 'alumno_id','id');
    }

    public function CalificacionParcial()
    {
        return $this->hasMany(CalificacionParcial::class, 'usuario_id','id');
    }

    /**
     * Cuando el usuario es de tipo Alumno, puede hacer uso de la relacion tutor para obtener a su tutor, solo debe tener un tutor
     */
    public function Tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id','id')->with('datosGenerales');
    }

    public function Tutorados()
    {
        return $this->hasMany(User::class, 'tutor_id','id')->with('datosGenerales');
    }

    /**
     * Set the email_verified_at.
     *
     * @param  string  $value
     * @return void
     */
    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = !empty($value) ? \DateTime::createFromFormat('Y-m-d H:i:s', $value) : null;
    }

    /**
     * Get email_verified_at in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getEmailVerifiedAtAttribute($value)
    {
        return $value != null ?  \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }

    /**
     * Get created_at in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    /**
     * Get updated_at in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getUpdatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    /**
     * Get deleted_at in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getDeletedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }

}
