<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Article extends Model
{
	use HasFactory;

	protected $guarded = array('id');

	protected $fillable = [
		'user_id',
		'name',
		'content',
		'reply_id',
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function parent()
	{
		return $this->belongsTo(Article::class, 'reply_id');
	}

	public function replies()
	{
		return $this->hasMany(Article::class, 'reply_id')->orderBy('created_at', 'asc');
	}

	public function allReplies()
	{
		return $this->hasMany(Article::class, 'reply_id')->with('allReplies');
	}
}