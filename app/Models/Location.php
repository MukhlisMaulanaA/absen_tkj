<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
  use SoftDeletes;

  protected $fillable = ['name', 'latitude', 'longitude', 'radius', 'address'];

  public function users(): HasMany
  {
    return $this->hasMany(User::class);
  }
}