<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    // Menambahkan kolom deleted_at ke semua tabel terkait
    Schema::table('locations', function (Blueprint $table) {
      $table->softDeletes(); });
    Schema::table('schedules', function (Blueprint $table) {
      $table->softDeletes(); });
    Schema::table('users', function (Blueprint $table) {
      $table->softDeletes(); });
    Schema::table('attendances', function (Blueprint $table) {
      $table->softDeletes(); });
    Schema::table('overtime_requests', function (Blueprint $table) {
      $table->softDeletes(); });
  }

  public function down(): void
  {
    // Menghapus kembali kolom jika migration di-rollback
    Schema::table('locations', function (Blueprint $table) {  
      $table->dropSoftDeletes(); });
    Schema::table('schedules', function (Blueprint $table) {
      $table->dropSoftDeletes(); });
    Schema::table('users', function (Blueprint $table) {
      $table->dropSoftDeletes(); });
    Schema::table('attendances', function (Blueprint $table) {
      $table->dropSoftDeletes(); });
    Schema::table('overtime_requests', function (Blueprint $table) {
      $table->dropSoftDeletes(); });
  }
};