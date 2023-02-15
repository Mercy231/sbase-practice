<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'text',
        'user_id',
        'post_id',
        'comment_id'
    ];
    protected $with = [
        'user',
        'comment'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function comment()
    {
        return$this->hasMany(Comment::class,  'comment_id');
    }
}
