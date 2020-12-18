<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
  protected $table = 'tbl_sub_category';
  public $timestamps = false;
  protected $primaryKey = 'subcatid';
}