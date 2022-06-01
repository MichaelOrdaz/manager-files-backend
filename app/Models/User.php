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
        'name',
        'lastname',
        'second_lastname',
        'phone',
        'image',
        'password',
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
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'creator_id', 'id');
    }

    public function histories()
    {
        return $this->hasMany(History::class, 'user_id', 'id');
    }

    public function share()
    {
        return $this->belongsToMany(Document::class, 'document_user', 'user_id', 'document_id')
        ->withPivot('id', 'permission', 'granted_by')
        ->withTimestamps();
    }

    public function sharedGranted()
    {
        return $this->belongsToMany(Document::class, 'document_user', 'granted_by', 'document_id')
        ->withPivot('id', 'permission', 'user_id')
        ->withTimestamps();
    }

    /**
     *
     * @param User $user
     * @param Document|null $document
     * @return mixed bool|object
     * return false or usermodel with pivot information
     */
    public static function userHasAuthorizationToAccessDocument(User $user, ?Document $document)
    {
        if (is_null($document)) return false;
        //si existe el usuario tiene permiso, independiente del tipo de permiso
        $userShared = $document->share()
        ->where('users.id', $user->id)
        ->first();
        if ($userShared) {
            return $userShared;
        } else {
            return self::userHasAuthorizationToAccessDocument($user, $document->parent);
        }
    }
}