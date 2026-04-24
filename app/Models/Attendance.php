<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Attendance extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'user_id',
    'date',
    'check_in_time',
    'check_in_lat',
    'check_in_lng',
    'check_in_image',
    'check_out_time',
    'check_out_lat',
    'check_out_lng',
    'check_out_image',
    'is_late',
    'late_duration'
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function overtimeRequest(): HasOne
  {
    return $this->hasOne(OvertimeRequest::class);
  }
}
