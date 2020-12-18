<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BihEntry extends Model
{
  protected $table = 'bih_entries';
  public $timestamps = false;
  protected $fillable = ['email','name','story','file'];
}
