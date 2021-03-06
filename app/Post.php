<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
  use SoftDeletes;
  protected $table = 'posts';
  public $primaryKey = 'id';
  public $timestamps = true;
  protected $fillable = [
      'discussion_id', 'user_id', 'module_id', 'title', 'body'
  ];
  protected $hidden = ['files','created_at','updated_at','deleted_at','user'];
  protected $dates = ['deleted_at'];
  //override original delete method to also delete childs
  protected static function boot()
  {
    parent::boot();
    static::deleting(function($post) {
       foreach ($post->replies as $reply) {
          $reply->delete();
       }
       foreach ($post->files as $file) {
          $file->delete();
       }
    });
  }

  public function files()
  {
    return $this->hasMany('App\FileUp', 'relate_id')->where('relate_type', 'post');
  }
  public function discussion()
  {
    return $this->belongsTo('App\Discussion', 'discussion_id');
  }
  public function user()
  {
    return $this->belongsTo('App\User', 'user_id');
  }
  public function module()
  {
    return $this->belongsTo('App\Module', 'module_id');
  }
  public function replies()
  {
    return $this->hasMany('App\Reply', 'post_id')->orderBy('approved','desc')->latest();
  }
}
