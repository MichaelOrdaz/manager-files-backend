<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    protected $fillable = [
        'name',
        'description',
        'location',
        'tags',
    ];

    protected $hidden = [
        'deleted_at',
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

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function shared()
    {
        return $this->belongsToMany(User::class, 'document_user', 'document_id', 'user_id');
    }
}
