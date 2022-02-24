<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estado extends Model
{

    use SoftDeletes, SoftCascadeTrait, HasFactory;


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'estados';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'nombre',
        'activo'
    ];

    protected $softCascade = [
        'municipio',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Get the datosGenerale for this model.
     *
     * @return App\Models\DatosGenerale
     */
    public function datosGenerales()
    {
        return $this->hasOne('App\Models\DatosGenerales','estado_origen_id','id');
    }

    /**
     * Get the municipio for this model.
     *
     * @return App\Models\Municipio
     */
    public function municipio()
    {
        return $this->hasOne('App\Models\Municipio','estado_id','id');
    }

    public function municipios()
    {
        return $this->hasMany('App\Models\Municipio','estado_id','id');
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
