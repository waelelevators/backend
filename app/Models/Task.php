<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'user_id',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'task_tags', 'task_id', 'tag_id');
    }


    public function users()
    {
        return $this->belongsToMany(User::class, 'task_users', 'task_id', 'user_id');
    }
}
