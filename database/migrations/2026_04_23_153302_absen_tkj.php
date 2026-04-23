<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    // 1. Tabel Locations (Master Data Geofencing)
    Schema::create('locations', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->decimal('latitude', 10, 8);
      $table->decimal('longitude', 11, 8);
      $table->integer('radius')->default(50)->comment('Dalam satuan meter');
      $table->text('address')->nullable();
      $table->timestamps();
    });

    // 2. Tabel Schedules (Master Data Waktu Kerja)
    Schema::create('schedules', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->time('check_in_time');
      $table->time('check_out_time');
      $table->timestamps();
    });

    // 3. Update Tabel Users Bawaan Laravel
    Schema::table('users', function (Blueprint $table) {
      $table->enum('role', ['employee', 'supervisor', 'admin'])->default('employee')->after('password');
      $table->string('device_id')->nullable()->after('role')->comment('Untuk lock fingerprint/device ID');
      $table->foreignId('location_id')->nullable()->after('device_id')->constrained('locations')->nullOnDelete();
      $table->foreignId('schedule_id')->nullable()->after('location_id')->constrained('schedules')->nullOnDelete();
    });

    // 4. Tabel Attendances (Transaksi Absensi Harian)
    Schema::create('attendances', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
      $table->date('date');

      // Check-in Data
      $table->dateTime('check_in_time');
      $table->decimal('check_in_lat', 10, 8);
      $table->decimal('check_in_lng', 11, 8);

      // Check-out Data (Nullable karena diisi nanti saat pulang)
      $table->dateTime('check_out_time')->nullable();
      $table->decimal('check_out_lat', 10, 8)->nullable();
      $table->decimal('check_out_lng', 11, 8)->nullable();

      // Status Data
      $table->boolean('is_late')->default(false);
      $table->integer('late_duration')->default(0)->comment('Durasi telat dalam menit');

      $table->timestamps();

      // Indexing untuk mempercepat query filter 2 mingguan di dashboard Admin
      $table->index(['user_id', 'date']);
    });

    // 5. Tabel Overtime Requests (Pengajuan Lembur 17:00 - 02:00)
    Schema::create('overtime_requests', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
      $table->foreignId('attendance_id')->constrained('attendances')->cascadeOnDelete();

      $table->dateTime('request_time');
      $table->text('description');

      // Status default adalah pending
      $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

      // Data Approval Atasan
      $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
      $table->dateTime('approved_at')->nullable();

      $table->timestamps();

      // Indexing untuk mempercepat query Admin/Atasan melihat request yang belum diproses
      $table->index('status');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    // Drop tabel harus berurutan dari yang paling dependent (terikat)
    Schema::dropIfExists('overtime_requests');
    Schema::dropIfExists('attendances');

    // Kembalikan tabel users seperti semula sebelum di-drop master datanya
    Schema::table('users', function (Blueprint $table) {
      $table->dropForeign(['schedule_id']);
      $table->dropForeign(['location_id']);
      $table->dropColumn(['role', 'device_id', 'location_id', 'schedule_id']);
    });

    Schema::dropIfExists('schedules');
    Schema::dropIfExists('locations');
  }
};