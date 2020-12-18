<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
  protected $table = 'tbl_user';
  public $timestamps = false;
  protected $primaryKey = 'userid';
  protected $fillable = ['catid','fname','lname','email'];
  protected $hidden = ['password'];
}
