<x-filament-panels::page class="!p-0 sm:!p-6">
  <div class="w-full max-w-2xl mx-auto px-4 sm:px-0" x-data="attendanceForm()">

    <!-- Header Card -->
    <div
      class="bg-gradient-to-br from-primary-600 to-primary-700 dark:from-primary-700 dark:to-primary-900 rounded-2xl sm:rounded-3xl shadow-lg p-6 sm:p-8 text-center mb-6">
      <div
        class="w-16 h-16 mx-auto bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mb-4">
        <x-heroicon-o-clock class="w-8 h-8 text-white" />
      </div>
      <h2 class="text-2xl sm:text-3xl font-bold text-white">
        {{ now()->translatedFormat('l, d F Y') }}
      </h2>
      <div class="text-white/90 mt-3 flex items-center justify-center space-x-2 text-sm font-medium">
        <x-heroicon-s-map-pin class="w-5 h-5" />
        <span>{{ auth()->user()->location->name ?? 'Lokasi Belum Diatur' }}</span>
      </div>
      <!-- GPS Status Indicator -->
      <div class="mt-4 flex items-center justify-center space-x-2 text-xs text-white/80">
        <div class="w-2 h-2 rounded-full" :class="isLoading ? 'bg-yellow-300 animate-pulse' : 'bg-green-300'"></div>
        <span x-text="isLoading ? 'Mencari GPS...' : 'GPS Siap'"></span>
      </div>
    </div>

    <!-- Main Content Card -->
    <div
      class="bg-white dark:bg-gray-900 rounded-2xl sm:rounded-3xl shadow-lg p-6 sm:p-8 border border-gray-100 dark:border-gray-800 relative overflow-hidden">

      <!-- Loading Overlay -->
      <div x-show="isLoading" style="display: none;"
        class="absolute inset-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm flex flex-col items-center justify-center transition-all">
        <div class="flex flex-col items-center gap-4">
          <x-heroicon-o-arrow-path class="w-14 h-14 sm:w-16 sm:h-16 text-primary-600 animate-spin" />
          <p class="text-lg sm:text-xl font-bold text-gray-800 dark:text-white text-center px-4" x-text="loadingText"></p>
          <p class="text-gray-500 text-sm mt-2 px-6 text-center leading-relaxed">{{ $todayAttendance && $todayAttendance->check_out_time ? 'Tunggu sebentar...' : 'Pastikan GPS aktif & Anda di lokasi yang tepat.' }}</p>
          <div class="mt-4 flex gap-1">
            <div class="w-2 h-2 bg-primary-600 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
            <div class="w-2 h-2 bg-primary-600 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
            <div class="w-2 h-2 bg-primary-600 rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
          </div>
        </div>
      </div>

      <!-- Camera/Photo Section -->
      @if (!$todayAttendance || ($todayAttendance && !$todayAttendance->check_out_time))
        <div class="mb-8 sm:mb-10">
          <label
            class="block text-sm sm:text-base font-bold text-gray-700 dark:text-gray-300 mb-6 text-center uppercase tracking-wider">
            📸 {{ !$todayAttendance ? 'Selfie Kehadiran' : 'Selfie Kepulangan' }}
          </label>

          @if ($photo)
            <div class="relative mx-auto w-56 h-56 sm:w-64 sm:h-64 rounded-3xl overflow-hidden border-4 border-primary-500 shadow-2xl ring-4 ring-primary-500/10">
              <img src="{{ $photo->temporaryUrl() }}" class="object-cover w-full h-full" alt="Preview Selfie">
              <button wire:click="$set('photo', null)"
                class="absolute top-3 right-3 bg-danger-600 hover:bg-danger-700 text-white rounded-full p-3 shadow-lg transition transform hover:scale-110 active:scale-95">
                <x-heroicon-o-trash class="w-6 h-6" />
              </button>
            </div>
          @else
            <div
              class="relative mx-auto w-56 h-56 sm:w-64 sm:h-64 rounded-3xl overflow-hidden border-4 border-dashed border-gray-300 dark:border-gray-600 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 hover:border-primary-500 dark:hover:border-primary-400 transition flex flex-col items-center justify-center cursor-pointer group shadow-inner">
              <div class="relative z-10">
                <div class="p-4 bg-white dark:bg-gray-700 rounded-full shadow-md group-hover:scale-110 group-hover:shadow-lg transition">
                  <x-heroicon-o-camera class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400 group-hover:text-primary-500 transition" />
                </div>
                <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 font-medium mt-4 block text-center">Ketuk untuk Ambil Foto</span>
              </div>
              <input type="file" wire:model.live="photo" accept="image/*" capture="user"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
            </div>
          @endif

          @error('photo')
            <div
              class="mt-4 text-danger-600 dark:text-danger-400 text-sm font-bold bg-danger-50 dark:bg-danger-900/30 p-4 rounded-xl border border-danger-200 dark:border-danger-800 text-center">
              ⚠️ {{ $message }}
            </div>
          @enderror
        </div>
      @endif

      <!-- Check-In Button -->
      @if (!$todayAttendance)
        <button x-on:click="getLocationAndSubmit()"
          class="w-full min-h-14 sm:min-h-16 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-500 hover:to-primary-600 active:from-primary-700 active:to-primary-800 text-white font-bold text-lg sm:text-xl rounded-2xl shadow-lg shadow-primary-500/30 transition-all active:scale-95 flex items-center justify-center gap-3 px-4">
          <x-heroicon-o-finger-print class="w-7 h-7 sm:w-8 sm:h-8" />
          Absen Masuk
        </button>
      <!-- Check-Out Section -->
      @elseif($todayAttendance && !$todayAttendance->check_out_time)
        <div
          class="w-full bg-gradient-to-r from-success-50 to-emerald-50 dark:from-success-900/20 dark:to-emerald-900/20 text-success-700 dark:text-success-300 p-5 sm:p-6 rounded-2xl mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center border-2 border-success-200 dark:border-success-800/50 gap-4 sm:gap-0">
          <div>
            <p class="text-xs font-bold uppercase tracking-wider opacity-80">✓ Check-in Berhasil</p>
            <p class="text-2xl sm:text-3xl font-black mt-1">{{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }}
              WIB</p>
          </div>
          <div class="hidden sm:flex w-12 h-12 bg-success-100 dark:bg-success-800/50 rounded-full items-center justify-center flex-shrink-0">
            <x-heroicon-s-check class="w-7 h-7 sm:w-8 sm:h-8" />
          </div>
        </div>

        <button x-on:click="getLocationAndSubmit()"
          class="w-full min-h-14 sm:min-h-16 bg-gradient-to-r from-warning-500 to-amber-500 hover:from-warning-400 hover:to-amber-400 active:from-warning-600 active:to-amber-600 text-white font-bold text-lg sm:text-xl rounded-2xl shadow-lg shadow-warning-500/30 transition-all active:scale-95 flex items-center justify-center gap-3 px-4">
          <x-heroicon-o-arrow-right-start-on-rectangle class="w-7 h-7 sm:w-8 sm:h-8" />
          Absen Pulang
        </button>
      <!-- Completion Message -->
      @else
        <div class="w-full text-center py-8 sm:py-12">
          <div
            class="inline-flex items-center justify-center w-28 h-28 sm:w-32 sm:h-32 bg-gradient-to-br from-success-100 to-emerald-100 dark:from-success-900/30 dark:to-emerald-900/30 rounded-full mb-6 relative">
            <div class="absolute inset-0 bg-success-500 rounded-full animate-ping opacity-20"></div>
            <x-heroicon-o-check-badge class="w-16 h-16 sm:w-20 sm:h-20 text-success-600 dark:text-success-400 relative z-10" />
          </div>
          <h3 class="text-3xl sm:text-4xl font-black text-gray-800 dark:text-gray-100">Selesai! ✓</h3>
          <p class="text-gray-500 dark:text-gray-400 mt-3 font-medium text-base sm:text-lg">Data kehadiran Anda hari ini telah tersimpan.</p>
          <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <p class="text-sm text-blue-700 dark:text-blue-300">📍 Lokasi: <span class="font-semibold">{{ auth()->user()->location->name }}</span></p>
            <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">⏰ Waktu: <span class="font-semibold">{{ $todayAttendance->check_in_time->format('H:i') }} - {{ $todayAttendance->check_out_time?->format('H:i') ?? '-' }}</span></p>
          </div>
        </div>
      @endif
    </div>

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('attendanceForm', () => ({
        isLoading: false,
        loadingText: '',
        gpsRetries: 0,
        maxRetries: 2,

        getLocationAndSubmit() {
          // Validasi browser support
          if (!navigator.geolocation) {
            this.showError("Browser Anda tidak mendukung fitur GPS/Geolocation. Gunakan browser modern (Chrome, Firefox, Safari).");
            return;
          }

          this.gpsRetries = 0;
          this.requestLocation();
        },

        requestLocation() {
          this.isLoading = true;
          this.loadingText = this.gpsRetries === 0 
            ? '📡 Menghubungi Satelit GPS...' 
            : `📡 Mencoba Ulang GPS (${this.gpsRetries}/${this.maxRetries})...`;

          const gpsOptions = {
            enableHighAccuracy: true,  // Menggunakan GPS akurat tinggi
            timeout: 20000,             // 20 detik timeout
            maximumAge: 0               // Tidak menggunakan cached location
          };

          navigator.geolocation.getCurrentPosition(
            (position) => {
              this.loadingText = '🔍 Memverifikasi Radius Lokasi...';
              
              // Kirim data GPS ke server
              $wire.submitAttendance(
                position.coords.latitude, 
                position.coords.longitude,
                position.coords.accuracy
              )
                .then(() => {
                  this.isLoading = false;
                })
                .catch((error) => {
                  this.isLoading = false;
                  console.error('Submission error:', error);
                });
            },
            (error) => {
              this.handleGpsError(error);
            },
            gpsOptions
          );
        },

        handleGpsError(error) {
          this.isLoading = false;
          let title = "GPS Error";
          let message = "Terjadi kesalahan GPS.";
          let actionText = "Coba Lagi";

          switch (error.code) {
            case error.PERMISSION_DENIED:
              title = "❌ Izin GPS Ditolak";
              message = "Mohon izinkan akses lokasi di pengaturan browser:\n\n"
                + "1. Klik ikon gembok/info di address bar\n"
                + "2. Pilih 'Izinkan' untuk lokasi\n"
                + "3. Refresh halaman ini\n"
                + "4. Coba lagi";
              break;

            case error.POSITION_UNAVAILABLE:
              if (this.gpsRetries < this.maxRetries) {
                this.gpsRetries++;
                setTimeout(() => this.requestLocation(), 1500);
                return;
              }
              title = "📍 Sinyal GPS Lemah";
              message = "GPS tidak stabil atau tidak tersedia.\n\n"
                + "Tips:\n"
                + "• Pindah ke area outdoor terbuka\n"
                + "• Jauhkan dari gedung tinggi\n"
                + "• Tunggu 30 detik untuk GPS lock\n"
                + "• Pastikan GPS di Settings aktif";
              break;

            case error.TIMEOUT:
              if (this.gpsRetries < this.maxRetries) {
                this.gpsRetries++;
                setTimeout(() => this.requestLocation(), 1500);
                return;
              }
              title = "⏱️ GPS Timeout";
              message = "Pencarian GPS terlalu lama.\n\n"
                + "Pastikan:\n"
                + "• GPS enabled di Settings\n"
                + "• Anda di lokasi outdoor\n"
                + "• Signal satelit kuat\n"
                + "• Koneksi internet stabil";
              break;
          }

          this.showError(message, title, actionText);
        },

        showError(message, title = "Kesalahan", actionText = "Coba Lagi") {
          Swal.fire({
            title: title,
            text: message,
            icon: 'error',
            confirmButtonText: actionText,
            confirmButtonColor: '#dc2626',
            allowOutsideClick: false,
            didOpen: () => {
              const confirmBtn = Swal.getConfirmButton();
              if (confirmBtn) {
                confirmBtn.style.minWidth = '44px';
                confirmBtn.style.minHeight = '44px';
                confirmBtn.style.fontSize = '16px';
              }
            }
          }).then(() => {
            this.getLocationAndSubmit();
          });
        }
      }));
    });
  </script>
</x-filament-panels::page>
