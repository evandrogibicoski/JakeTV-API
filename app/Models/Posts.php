<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
  protected $table = 'tbl_post';
  public $timestamps = false;
  protected $primaryKey = 'postid';
  protected $fillable = ['title', 'catid', 'image', 'url', 'kicker', 'source', 'description', 'publish'];
}
