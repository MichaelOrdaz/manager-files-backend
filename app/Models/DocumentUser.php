<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentUser extends Model
{
    use HasFactory;

    protected $table = 'document_user';

    protected $fillable = [
        'permission',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'user_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'document_id', 'id');
    }

    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by', 'id');
    }

}
