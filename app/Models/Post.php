<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comment;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'category',
        'text',
        'user_id'
    ];

    protected $with = [
        'user',
        'comment'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function comment()
    {
        return $this->hasMany(Comment::class)->where('comment_id', '=', null);
    }
}
