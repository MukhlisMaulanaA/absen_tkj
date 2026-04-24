<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
  use SoftDeletes;

  protected $fillable = ['name', 'check_in_time', 'check_out_time'];

  public function users(): HasMany
  {
    return $this->hasMany(User::class);
  }
}