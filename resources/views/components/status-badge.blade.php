@props([
    'status' => '',
])

@php
    $map = [
        'hadir' => ['bg-status-hadir-bg text-status-hadir-text dark:bg-green-900/30 dark:text-green-400', 'Hadir'],
        'izin' => ['bg-status-izin-bg text-status-izin-text dark:bg-blue-900/30 dark:text-blue-400', 'Izin'],
        'sakit' => ['bg-status-sakit-bg text-status-sakit-text dark:bg-yellow-900/30 dark:text-yellow-400', 'Sakit'],
        'alfa' => ['bg-status-alfa-bg text-status-alfa-text dark:bg-red-900/30 dark:text-red-400', 'Alfa'],
        'menunggu' => ['bg-status-pending-bg text-status-pending-text dark:bg-orange-900/30 dark:text-orange-400', 'Menunggu'],
        'pending' => ['bg-status-pending-bg text-status-pending-text dark:bg-orange-900/30 dark:text-orange-400', 'Menunggu'],
        'disetujui' => ['bg-status-hadir-bg text-status-hadir-text dark:bg-green-900/30 dark:text-green-400', 'Disetujui'],
        'approved' => ['bg-status-hadir-bg text-status-hadir-text dark:bg-green-900/30 dark:text-green-400', 'Disetujui'],
        'ditolak' => ['bg-status-alfa-bg text-status-alfa-text dark:bg-red-900/30 dark:text-red-400', 'Ditolak'],
        'rejected' => ['bg-status-alfa-bg text-status-alfa-text dark:bg-red-900/30 dark:text-red-400', 'Ditolak'],
        'dibatalkan' => ['bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300', 'Dibatalkan'],
        'belum' => ['bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300', 'Belum'],
    ];
    [$classes, $label] = $map[$status] ?? ['bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300', ucfirst((string) $status)];
@endphp

<span class="inline-flex items-center justify-center px-3 py-1.5 rounded-full text-[11px] font-bold tracking-wide {{ $classes }}">
    {{ $label }}
</span>
