<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\User;
use App\Reply;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fname',
        'lname',
        'username',
        'email',
        'password',
        'dep_id',
        'gender',
        'role_id',
        'location',
        'level',
        'gpa',
        'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setFnameAttribute($value)
    {
        return $this->attributes['fname'] = ucfirst($value);
    }

    public function setLnameAttribute($value)
    {
        return $this->attributes['lname'] = ucfirst($value);
    }


    public function isAdmin()
    {
        if (!$this->role == 'admin')
        {
            return false;
        }
        return true;
    }
    public function isInstructor()
    {
      return ($this->role->name == 'Instructor' || $this->role->name == 'instructor');
    }
    public static function getStudents()
    {
      $role_id = ('App\Role')::where('name', 'student')->first()->id;
      return User::where('role_id', $role_id);
    }
    public static function getInstructors()
    {
      $role_id = ('App\Role')::where('name', 'instructor')->first()->id;
      return User::where('role_id', $role_id);
    }

  	public function department(){
  		return $this->belongsTo('App\Department', 'dep_id');
    }

    public function role(){
	     return $this->belongsTo('App\Role');
    }

    //Function to get messages between this user & friend ($friend_id)
    public function messages($friend_id)
    {
      if ($friend_id !== $this->id){
        //Get Sent messages of this user & friend ($friend_id)
        $sent = $this->hasMany('App\Message' , 'user_id')->where('friend_id', $friend_id);
        //Get Received messages of this user & friend ($friend_id)
        $received = $this->hasMany('App\Message' , 'friend_id')->where('user_id', $friend_id);
        //Union Both Sent & received then return the result
        $result = $received->union($sent)->orderBy('created_at', 'asc')->get();
        return $result;
      }else{
        return 0;
      }

    }
    // public function latestWithFriend($friend_id){
    //   if ($friend_id !== $this->id){
    //     $sent = $this->hasMany('App\Message' , 'user_id')->where('friend_id', $friend_id)->latest();
    //     $received = $this->hasMany('App\Message' , 'friend_id')->where('user_id', $friend_id)->latest();
    //     $result = $received->union($sent)->latest()->first();
    //     return $result;
    //   }
    // }
    public function msg_list(){
      $sent = $this->hasMany('App\Message', 'user_id')
      ->whereRaw('`messages`.`id` IN (SELECT MAX(`messages`.`id`) FROM `messages` GROUP BY `messages`.`friend_id`)');
      $received = $this->hasMany('App\Message', 'friend_id')
      ->whereRaw('`messages`.`id` IN (SELECT MAX(`messages`.`id`) FROM `messages` GROUP BY `messages`.`user_id`)');
      $result = $received->union($sent)->orderBy('created_at', 'asc')->limit(8);
      // $test = $this->hasMany('App\Message', 'user_id')->whereRaw('`messages`.`id` IN (SELECT MAX(`messages`.`id`) FROM `messages` GROUP BY `messages`.`friend_id`)');
      return $result;
    }
    //
    // public function lastSent($friend_id)
    // {
    //   return $this->hasMany('App\Message' , 'user_id')->where('friend_id', $friend_id)->latest()->first();
    // }
    //
    // public function lastReceived($friend_id)
    // {
    //   //Get Received messages of this user & friend ($friend_id)
    //   return $this->hasMany('App\Message' , 'friend_id')->where('user_id', $friend_id)->latest()->first();
    // }
    public function posts()
    {
      return $this->hasMany('App\Post', 'user_id');
    }
    public function replies()
    {
      return $this->hasMany('App\Reply', 'user_id');
    }
    public function votes()
    {
      return $this->hasMany('App\Vote', 'user_id');
    }
    public function Voted(Reply $reply)
    {
      return (bool) $reply->votes
                          ->where('reply_id', $reply->id)
                          ->where('user_id', $this->id)
                          ->count();
    }
}
