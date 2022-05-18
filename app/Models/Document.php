<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documents';

    protected $fillable = [
        'name',
        'description',
        'location',
        'date',
        'min_identifier',
        'max_identifier',
        'tags',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected $casts = [
        'tags' => 'array'
    ];

    protected $attributes = [
        'tags' => "[]",
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(DocumentType::class, 'type_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Document::class, 'parent_id', 'id');
    }

    public function parents()
    {
        return $this->belongsTo(Document::class, 'parent_id', 'id')->with('parents');
    }

    public function children() {
        return $this->hasMany(Document::class, 'parent_id', 'id')->with('children');
    }

    public function sons() {
        return $this->hasMany(Document::class, 'parent_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function shared()
    {
        return $this->belongsToMany(User::class, 'document_user', 'document_id', 'user_id');
    }
    
    public function historical()
    {
        return $this->hasMany(History::class, 'document_id', 'id');
    }
}
