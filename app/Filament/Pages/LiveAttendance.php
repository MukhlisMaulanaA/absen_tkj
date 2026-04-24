<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Livewire\WithFileUploads; // Tambahkan ini
use Illuminate\Support\Facades\Storage; // Tambahkan ini
use BackedEnum;

class LiveAttendance extends Page
{
  use WithFileUploads; // Gunakan trait ini
  protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-finger-print';
  protected static ?string $navigationLabel = 'Absen Sekarang';
  protected static ?string $title = 'Live Absensi';
  protected string $view = 'filament.pages.live-attendance';
  public $todayAttendance;
  public $photo; // Properti untuk menampung foto selfie

  public function mount()
  {
    // Cek apakah user sudah absen hari ini
    $this->todayAttendance = Attendance::where('user_id', auth()->id())
      ->where('date', now()->toDateString())
      ->first();
  }

  public function submitAttendance($lat, $lng, $accuracy = null)
  {
    // Validasi wajib foto
    $this->validate([
      'photo' => 'required|image|max:5120', // Maksimal 5MB
    ], [
      'photo.required' => 'Foto selfie wajib diisi sebelum absen.',
      'photo.image' => 'File harus berupa gambar.',
      'photo.max' => 'Ukuran foto maksimal 5MB.',
    ]);

    $user = auth()->user();

    // 1. Validasi User punya Lokasi & Jadwal
    if (!$user->location || !$user->schedule) {
      Notification::make()
        ->title('❌ Konfigurasi Tidak Lengkap')
        ->body('Admin belum mengatur Lokasi atau Jadwal untuk Anda.')
        ->danger()
        ->send();
      return;
    }

    // 2. Validasi GPS Accuracy (opsional tapi sebaiknya < 20 meter)
    if ($accuracy && $accuracy > 30) {
      Notification::make()
        ->title('⚠️ Akurasi GPS Rendah')
        ->body("Akurasi GPS Anda {$accuracy}m. Pindah ke area yang lebih terbuka untuk akurasi lebih baik.")
        ->warning()
        ->send();
      // Biarkan user melanjutkan meski akurasi rendah, tapi beri warning
    }

    // 3. Validasi Geofencing (Jarak)
    $distance = $this->calculateDistance($lat, $lng, $user->location->latitude, $user->location->longitude);
    $tolerance = 5; // Toleransi 5 meter

    if ($distance > ($user->location->radius + $tolerance)) {
      $outsideBy = round($distance - $user->location->radius);
      Notification::make()
        ->title('📍 Di Luar Jangkauan Lokasi')
        ->body("Anda berada {$outsideBy}m di luar radius lokasi ({$user->location->radius}m). Silakan pindah ke area kerja.")
        ->danger()
        ->send();
      return;
    }

    // Jika masih dalam toleransi, beri warning
    if ($distance > $user->location->radius) {
      Notification::make()
        ->title('⚠️ Lokasi Sangat Dekat Batas')
        ->body("Anda hanya " . round($distance) . "m dari batas area. Pastikan posisi sudah tepat.")
        ->warning()
        ->send();
    }

    $now = now();
    $imagePath = $this->photo->store('attendance-photos', 'public');

    // 3. Logika Check-In atau Check-Out
    if (!$this->todayAttendance) {
      // Proses CHECK-IN
      $isLate = $now->format('H:i:s') > $user->schedule->check_in_time;
      $lateDuration = $isLate ? Carbon::parse($user->schedule->check_in_time)->diffInMinutes($now) : 0;

      $this->todayAttendance = Attendance::create([
        'user_id' => $user->id,
        'date' => $now->toDateString(),
        'check_in_time' => $now,
        'check_in_lat' => $lat,
        'check_in_lng' => $lng,
        'check_in_image' => $imagePath,
        'is_late' => $isLate,
        'late_duration' => $lateDuration,
      ]);

      $message = $isLate
        ? "Check-in Berhasil (Terlambat {$lateDuration} menit)"
        : "Check-in Tepat Waktu ✓";

      Notification::make()
        ->title('✓ Check-in Berhasil')
        ->body($message)
        ->success()
        ->send();

    } else if (!$this->todayAttendance->check_out_time) {
      // Proses CHECK-OUT
      $checkInTime = Carbon::parse($this->todayAttendance->check_in_time);
      $workDuration = $checkInTime->diffInHours($now);

      $this->todayAttendance->update([
        'check_out_time' => $now,
        'check_out_lat' => $lat,
        'check_out_lng' => $lng,
        'check_out_image' => $imagePath,
      ]);

      Notification::make()
        ->title('✓ Check-out Berhasil')
        ->body("Total kerja: {$workDuration} jam")
        ->success()
        ->send();
    } else {
      Notification::make()
        ->title('ℹ️ Sudah Selesai')
        ->body('Anda sudah check-out hari ini.')
        ->info()
        ->send();
    }
    
    $this->photo = null;
    // Refresh state
    $this->mount();
  }

  // Rumus Haversine untuk hitung jarak (Meter) dengan presisi tinggi
  private function calculateDistance($lat1, $lon1, $lat2, $lon2)
  {
    // Konstanta radius bumi dalam meter
    $earthRadius = 6371000;
    
    // Konversi derajat ke radian
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    // Formula Haversine
    $a = sin($dLat / 2) * sin($dLat / 2) + 
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
         sin($dLon / 2) * sin($dLon / 2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c;
    
    // Return jarak dengan presisi 1 meter
    return round($distance, 1);
  }
}