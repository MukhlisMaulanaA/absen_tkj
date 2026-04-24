<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OvertimeRequest extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'user_id',
    'attendance_id',
    'request_time',
    'description',
    'status',
    'approved_by',
    'approved_at'
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function attendance(): BelongsTo
  {
    return $this->belongsTo(Attendance::class);
  }

  public function approver(): BelongsTo
  {
    return $this->belongsTo(User::class, 'approved_by');
  }
}