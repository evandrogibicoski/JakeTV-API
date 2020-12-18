<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  protected $table = 'tbl_category';
  public $timestamps = false;
  protected $primaryKey = 'catid';

  protected $fillable = ['category', 'catidu', 'status', 'cr_date', 'modify_date'];
}