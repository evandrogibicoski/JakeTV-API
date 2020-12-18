<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
  protected $table = 'tbl_admin';
  public $timestamps = false;
  protected $primaryKey = 'adminid';
  protected $hidden = ['password'];
}
