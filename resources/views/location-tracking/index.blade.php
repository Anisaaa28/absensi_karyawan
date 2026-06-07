@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="glass-panel px-6 py-7 sm:px-8" data-reveal>
        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr] xl:items-end">
            <div>
                <p class="section-title">Location Tracking Map</p>
                <h1 class="page-title">Peta GPS real-time untuk memverifikasi kehadiran dan pergerakan personel lapangan.</h1>
                
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="panel-outline rounded-[24px] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">GPS Active</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $locations->count() }}</p>
                </div>
                <div class="panel-outline rounded-[24px] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Office Point</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $offices->count() }}</p>
                </div>
                <div class="panel-outline rounded-[24px] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Refresh</p>
                    <p class="mt-3 text-lg font-semibold text-white">Realtime-ready</p>
                </div>
            </div>
        </div>
    </section>

    <section class="card-premium overflow-hidden" data-reveal>
        <div class="flex flex-col gap-3 border-b border-white/10 px-6 py-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="section-title">Leaflet Map</p>
                <h2 class="page-title text-2xl md:text-3xl">Area pelacakan lokasi</h2>
            </div>
        </div>
        <div id="tracking-map" class="h-[420px] w-full md:h-[560px]"></div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <article class="card-premium overflow-hidden" data-reveal>
            <div class="border-b border-white/10 px-6 py-5">
                <p class="section-title">Location Feed</p>
                <h2 class="page-title text-2xl md:text-3xl">Karyawan dengan GPS aktif</h2>
            </div>
            <div class="premium-scrollbar overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="table-header">
                        <tr>
                            <th>Karyawan</th>
                            <th>Koordinat</th>
                            <th>Update</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-body divide-y divide-slate-800">
                        @forelse($locations as $attendance)
                            <tr>
                                <td>
                                    <p class="font-semibold text-white">{{ $attendance->employee->name ?? 'N/A' }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $attendance->employee->nik ?? '-' }}</p>
                                </td>
                                <td class="text-slate-300">{{ $attendance->latitude }}, {{ $attendance->longitude }}</td>
                                <td class="text-slate-300">{{ $attendance->updated_at?->diffForHumans() ?? '-' }}</td>
                                <td>
                                    @if($attendance->status === 'Present')
                                        <span class="badge-present">Present</span>
                                    @elseif($attendance->status === 'Late')
                                        <span class="badge-late">Late</span>
                                    @else
                                        <span class="badge-onleave">{{ $attendance->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">Belum ada data GPS hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <aside class="space-y-6">
            <article class="card-premium" data-reveal>
                <p class="section-title">Office Zones</p>
                <div class="mt-6 space-y-4">
                    @forelse($offices as $office)
                        <div class="panel-outline rounded-[22px] p-4">
                            <p class="font-semibold text-white">{{ $office->name }}</p>
                            <p class="mt-1 text-sm text-slate-300">{{ $office->latitude }}, {{ $office->longitude }}</p>
                            <p class="mt-2 text-xs text-slate-400">Radius {{ $office->radius }} m</p>
                        </div>
                    @empty
                        <div class="empty-state text-slate-400">Belum ada office location.</div>
                    @endforelse
                </div>
            </article>
        </aside>
    </section>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let mapInstance = null;
    document.addEventListener('turbo:load', () => {
        if (!document.getElementById('tracking-map')) return;
        
        if (mapInstance !== null) {
            mapInstance.remove();
        }

        @php
            $employeePointsArray = $locations->map(function ($location) {
                return [
                    'name' => $location->employee->name ?? 'N/A',
                    'lat' => (float) $location->latitude,
                    'lng' => (float) $location->longitude,
                    'status' => $location->status,
                ];
            })->values()->toArray();

            $officesArray = $offices->map(function ($office) {
                return [
                    'name' => $office->name,
                    'lat' => (float) $office->latitude,
                    'lng' => (float) $office->longitude,
                    'radius' => (int) $office->radius,
                ];
            })->values()->toArray();
        @endphp
        const employeePoints = @json($employeePointsArray);
        const offices = @json($officesArray);

        const defaultLat = employeePoints[0]?.lat || offices[0]?.lat || -6.2;
        const defaultLng = employeePoints[0]?.lng || offices[0]?.lng || 106.8;

        mapInstance = L.map('tracking-map').setView([defaultLat, defaultLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(mapInstance);

        offices.forEach(office => {
            L.circle([office.lat, office.lng], {
                radius: office.radius,
                color: '#38bdf8',
                fillColor: '#38bdf8',
                fillOpacity: 0.12,
            }).addTo(mapInstance).bindPopup(`${office.name}<br>Radius ${office.radius} m`);
        });

        employeePoints.forEach(point => {
            L.marker([point.lat, point.lng]).addTo(mapInstance).bindPopup(`${point.name}<br>${point.status}`);
        });
    });
</script>
@endsection
