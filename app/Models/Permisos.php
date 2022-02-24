<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permisos extends Model
{

    use SoftDeletes, SoftCascadeTrait, HasFactory;


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id',
                  'name',
                  'guard_name',
                  'is_view'
              ];

    protected $softCascade = [
        'modelHasPermission',
        'roleHasPermissions',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
               'deleted_at'
           ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Get the modelHasPermission for this model.
     *
     * @return App\Models\ModelHasPermission
     */
    public function modelHasPermission()
    {
        return $this->hasOne('App\Models\ModelHasPermission','permission_id','id');
    }

    /**
     * Get the roleHasPermissions for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function roleHasPermissions()
    {
        return $this->hasMany('App\Models\RoleHasPermission','permission_id','id');
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

}
