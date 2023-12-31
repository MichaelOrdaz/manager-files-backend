<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'department_id', 'id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'department_id', 'id');
    }
}
