@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="checkinKiosk()" x-init="init()" data-reveal>
    <section class="glass-panel relative overflow-hidden px-6 py-7 sm:px-8 lg:px-10">
        <div class="absolute inset-y-0 right-0 hidden w-[32%] bg-[radial-gradient(circle_at_center,rgba(56,189,248,0.18),transparent_64%)] lg:block"></div>
        <div class="relative grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
            <div>
                <p class="section-title">Kiosk / Mobile Check In</p>
                <h1 class="page-title">Zona scan QR paling dominan untuk flow check-in dan check-out yang super cepat.</h1>
                

                <div class="mt-8 grid gap-4 sm:grid-cols-3">
                    <div class="panel-outline rounded-[24px] p-4">
                        <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Mode</p>
                        <p class="mt-3 text-2xl font-semibold text-white" x-text="mode === 'in' ? 'Clock In' : 'Clock Out'"></p>
                    </div>
                    <div class="panel-outline rounded-[24px] p-4">
                        <p class="text-xs uppercase tracking-[0.24em] text-slate-400">GPS</p>
                        <p class="mt-3 text-2xl font-semibold text-white" x-text="gpsStatus"></p>
                    </div>
                    <div class="panel-outline rounded-[24px] p-4">
                        <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Accuracy</p>
                        <p class="mt-3 text-2xl font-semibold text-white" x-text="accuracyLabel"></p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3 xl:grid-cols-1">
                <button type="button" @click="setMode('in')" :class="mode === 'in' ? 'btn-primary' : 'btn-secondary'" class="w-full justify-center">
                    Clock In
                </button>
                <button type="button" @click="setMode('out')" :class="mode === 'out' ? 'btn-primary' : 'btn-secondary'" class="w-full justify-center">
                    Clock Out
                </button>
                <button type="button" @click="requestGps()" class="btn-secondary w-full justify-center">
                    Refresh GPS
                </button>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.45fr_0.75fr]">
        <article class="glass-panel relative overflow-hidden p-5 sm:p-6 lg:p-8" data-reveal>
            <div class="absolute inset-x-10 top-6 h-px bg-gradient-to-r from-transparent via-sky-400/40 to-transparent"></div>
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="section-title">Scanner Stage</p>
                    <h2 class="page-title text-2xl md:text-3xl">Panel utama scan QR untuk kiosk friendly</h2>
                    
                </div>
                
            </div>

            <div class="mt-8 rounded-[34px] border border-sky-400/15 bg-slate-950/55 p-4 sm:p-5">
                <div class="relative flex min-h-[420px] items-center justify-center overflow-hidden rounded-[28px] border border-white/10 bg-[radial-gradient(circle_at_center,rgba(14,165,233,0.10),rgba(2,6,23,0.8))]">
                    <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(transparent_0%,rgba(14,165,233,0.06)_50%,transparent_100%)]"></div>
                    <div class="pointer-events-none absolute inset-8 rounded-[28px] border border-dashed border-sky-300/35"></div>
                    <div class="pointer-events-none absolute inset-x-14 top-1/2 h-px -translate-y-1/2 bg-gradient-to-r from-transparent via-sky-300/70 to-transparent" :class="isScanning ? 'animate-scan-pulse' : ''"></div>
                    <div id="scanner" class="relative z-10 flex h-full min-h-[360px] w-full items-center justify-center text-center text-slate-300">
                        <div class="space-y-4 px-4">
                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-[26px] bg-white/5 text-3xl text-sky-300 shadow-glass">
                                QR
                            </div>
                            <div>
                                <p class="text-2xl font-semibold text-white">Arahkan QR ke area bingkai</p>
                                <p class="mt-2 text-sm text-slate-400">Transisi scan aktif, pulse line, dan camera preview akan muncul di sini.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 md:grid-cols-3">
                    <button id="start-scan" type="button" class="btn-primary justify-center">Mulai Scan</button>
                    <button id="stop-scan" type="button" class="btn-secondary justify-center">Stop Scan</button>
                    <button type="button" class="btn-secondary justify-center" @click="fillDemo()">
                        Isi NIK dari QR
                    </button>
                </div>
            </div>
        </article>

        <article class="glass-panel p-5 sm:p-6 lg:p-8" data-reveal>
            <div class="rounded-[28px] border border-white/10 bg-white/[0.04] p-5">
                <p class="section-title">Manual Backup</p>
                <h2 class="page-title text-2xl md:text-3xl">Input NIK & validasi GPS</h2>
                
            </div>

            <form id="checkin-form" action="{{ route('checkin.submit') }}" method="POST" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-300">NIK Karyawan</label>
                    <input x-model="nik" type="text" name="nik" required class="mt-3 w-full" placeholder="Contoh: 2024001" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300">Mode Absensi</label>
                    <select x-model="mode" name="mode" class="mt-3 w-full">
                        <option value="in">Clock In</option>
                        <option value="out">Clock Out</option>
                    </select>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="panel-outline rounded-[22px] p-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Latitude</p>
                        <p class="mt-2 text-sm font-semibold text-white" x-text="latitude || '-'"></p>
                    </div>
                    <div class="panel-outline rounded-[22px] p-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Longitude</p>
                        <p class="mt-2 text-sm font-semibold text-white" x-text="longitude || '-'"></p>
                    </div>
                </div>
                <input type="hidden" name="latitude" id="latitude" />
                <input type="hidden" name="longitude" id="longitude" />
                <input type="hidden" name="accuracy" id="accuracy" />
                <button type="submit" class="btn-primary w-full justify-center">Submit Absensi</button>
            </form>

            
        </article>
    </section>

    <!-- Scan Confirmation Modal -->
    <div x-show="showConfirmModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm" style="display: none;">
        <div @click.away="cancelScan()" x-show="showConfirmModal" x-transition.opacity.duration.300ms class="w-full max-w-sm rounded-3xl border border-slate-700/50 bg-slate-900/95 p-8 shadow-2xl">
            <div class="text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-sky-500/10 text-2xl text-sky-400 mb-4">
                    ✓
                </div>
                <h3 class="text-xl font-bold text-white">QR Terdeteksi</h3>
                <p class="text-sm text-slate-400 mt-1">Konfirmasi absensi karyawan</p>
            </div>
            
            <div class="mt-6 space-y-4">
                <div class="rounded-2xl border border-white/5 bg-white/[0.02] p-4 text-center">
                    <p class="text-lg font-semibold text-white" x-text="scannedName"></p>
                    <p class="text-sm text-sky-400 font-mono mt-1" x-text="nik"></p>
                    <p class="text-xs text-slate-500 mt-1" x-text="scannedType"></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Pilih Mode Absensi</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="mode = 'in'" :class="mode === 'in' ? 'bg-sky-500 text-white border-sky-400 shadow-[0_0_15px_rgba(14,165,233,0.3)]' : 'bg-slate-800 text-slate-400 border-slate-700 hover:bg-slate-700'" class="rounded-xl border py-3 text-sm font-semibold transition-all">Clock In</button>
                        <button type="button" @click="mode = 'out'" :class="mode === 'out' ? 'bg-rose-500 text-white border-rose-400 shadow-[0_0_15px_rgba(244,63,94,0.3)]' : 'bg-slate-800 text-slate-400 border-slate-700 hover:bg-slate-700'" class="rounded-xl border py-3 text-sm font-semibold transition-all">Clock Out</button>
                    </div>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-3">
                <button type="button" @click="cancelScan()" class="btn-secondary justify-center">Batal</button>
                <button type="button" @click="submitScan()" class="btn-primary justify-center">Submit</button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    function checkinKiosk() {
        return {
            nik: '{{ request('qr', '') }}',
            mode: '{{ request('mode', 'in') }}',
            latitude: '',
            longitude: '',
            accuracy: '',
            isScanning: false,
            showConfirmModal: false,
            scannedName: '',
            scannedType: '',
            isLoadingEmployee: false,
            get gpsStatus() {
                return this.latitude && this.longitude ? 'Locked' : 'Waiting';
            },
            get accuracyLabel() {
                return this.accuracy ? `${this.accuracy} m` : '-';
            },
            init() {
                this.requestGps();
            },
            setMode(value) {
                this.mode = value;
            },
            fillDemo() {
                if (!this.nik) {
                    this.nik = '{{ request('qr', '2024001') }}';
                }
            },
            async processScan(scannedNik) {
                if (this.showConfirmModal || this.isLoadingEmployee) return;
                
                this.nik = scannedNik;
                this.isLoadingEmployee = true;
                
                if (window.html5QrcodeScanner) {
                    window.html5QrcodeScanner.pause(true);
                }

                try {
                    const response = await fetch(`/checkin/employee/${scannedNik}`);
                    if (!response.ok) throw new Error('Karyawan tidak ditemukan');
                    
                    const data = await response.json();
                    this.scannedName = data.name;
                    this.scannedType = data.type;
                    this.showConfirmModal = true;
                } catch (error) {
                    if (window.AlpineUtilities) window.AlpineUtilities.error(error.message || 'Gagal memverifikasi NIK');
                    if (window.html5QrcodeScanner) window.html5QrcodeScanner.resume();
                } finally {
                    this.isLoadingEmployee = false;
                }
            },
            cancelScan() {
                this.showConfirmModal = false;
                this.nik = '';
                this.scannedName = '';
                if (window.html5QrcodeScanner) {
                    window.html5QrcodeScanner.resume();
                }
            },
            submitScan() {
                document.getElementById('checkin-form').submit();
            },
            requestGps() {
                if (!navigator.geolocation) return;
                navigator.geolocation.getCurrentPosition((position) => {
                    this.latitude = position.coords.latitude.toFixed(7);
                    this.longitude = position.coords.longitude.toFixed(7);
                    this.accuracy = Math.round(position.coords.accuracy);
                    document.getElementById('latitude').value = this.latitude;
                    document.getElementById('longitude').value = this.longitude;
                    document.getElementById('accuracy').value = this.accuracy;
                });
            }
        }
    }

    var html5QrcodeScanner = null;

    function startScanner() {
        const root = document.querySelector('[x-data^="checkinKiosk"]')?._x_dataStack?.[0];
        if (root) root.isScanning = true;
        if (html5QrcodeScanner) return;

        html5QrcodeScanner = new Html5Qrcode('scanner');
        html5QrcodeScanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 260, height: 260 }, aspectRatio: 1.3 },
            (qrCodeMessage) => {
                const root = document.querySelector('[x-data^="checkinKiosk"]')?._x_dataStack?.[0];
                if (root) {
                    root.processScan(qrCodeMessage.trim());
                }
            },
            () => {}
        ).catch(() => {
            if (window.AlpineUtilities) {
                window.AlpineUtilities.error('Kamera tidak dapat diakses. Pastikan izin kamera diizinkan di browser.');
            }
            html5QrcodeScanner = null;
            if (root) root.isScanning = false;
        });
    }

    function stopScanner() {
        const root = document.querySelector('[x-data^="checkinKiosk"]')?._x_dataStack?.[0];
        if (root) root.isScanning = false;
        if (!html5QrcodeScanner) return;

        html5QrcodeScanner.stop()
            .then(() => {
                html5QrcodeScanner.clear();
                html5QrcodeScanner = null;

                // Paksa matikan semua track kamera supaya lampu LED kamera mati
                if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
                    navigator.mediaDevices.getUserMedia({ video: true })
                        .then(stream => stream.getTracks().forEach(t => t.stop()))
                        .catch(() => {});
                }

                const scannerEl = document.getElementById('scanner');
                if (scannerEl) {
                    scannerEl.innerHTML = `
                        <div class="space-y-4 px-4 text-center text-slate-300">
                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-[26px] bg-white/5 text-3xl text-sky-300 shadow-glass">📷</div>
                            <div>
                                <p class="text-2xl font-semibold text-white">Kamera dimatikan</p>
                                <p class="mt-2 text-sm text-slate-400">Tekan <strong class="text-white">Mulai Scan</strong> untuk mengaktifkan kembali.</p>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(() => { html5QrcodeScanner = null; });
    }

    function initCheckinPage() {
        const startBtn = document.getElementById('start-scan');
        const stopBtn  = document.getElementById('stop-scan');
        if (!startBtn || !stopBtn) return;

        // Hapus event listener lama agar tidak duplikat saat Turbo Drive kembali ke halaman ini
        startBtn.replaceWith(startBtn.cloneNode(true));
        stopBtn.replaceWith(stopBtn.cloneNode(true));

        document.getElementById('start-scan').addEventListener('click', startScanner);
        document.getElementById('stop-scan').addEventListener('click', stopScanner);

        // Langsung buka kamera saat halaman tampil
        startScanner();
    }

    // Turbo:load menggantikan DOMContentLoaded agar berjalan setiap navigasi
    document.addEventListener('turbo:load', initCheckinPage);

    // Matikan kamera saat pengguna berpindah ke halaman lain
    document.addEventListener('turbo:before-visit', () => {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop()
                .then(() => { html5QrcodeScanner.clear(); html5QrcodeScanner = null; })
                .catch(() => { html5QrcodeScanner = null; });
        }
    });
</script>
@endsection
