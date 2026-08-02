<x-app-layout title="Absensi - Sistem Absensi UKM Taekwondo" mobile-header-title="Absensi">

    <x-page-header title="Absensi" subtitle="Arahkan QR Code anggota ke kamera untuk mencatat kehadiran" />

    <livewire:absensi-scan />

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
        <script>
            // Scanner QR kamera -> kirim hasil decode ke Livewire (sumber = scan).
            let scanCooldown = false;
            let html5QrCode = null;

            function onScanSuccess(decodedText) {
                if (scanCooldown) return;
                scanCooldown = true;
                setTimeout(() => { scanCooldown = false; }, 2000);

                Livewire.dispatch('scanResult', { idAnggota: decodedText });
            }

            function setReaderHint(text) {
                const hint = document.getElementById('qr-reader-hint');
                if (hint) hint.textContent = text;
            }

            function startScanner() {
                if (typeof Html5Qrcode === 'undefined') {
                    setReaderHint('Kamera tidak tersedia. Gunakan input NIM manual.');
                    return;
                }

                const reader = document.getElementById('qr-reader');
                if (!reader) return;

                html5QrCode = new Html5Qrcode('qr-reader');

                Html5Qrcode.getCameras()
                    .then((cameras) => {
                        if (!cameras || cameras.length === 0) {
                            setReaderHint('Kamera tidak ditemukan. Gunakan input NIM manual.');
                            return;
                        }
                        setReaderHint('Mengaktifkan kamera...');
                        return html5QrCode.start(
                            { facingMode: 'environment' },
                            { fps: 10, qrbox: { width: 220, height: 220 } },
                            onScanSuccess
                        );
                    })
                    .then(() => setReaderHint('Arahkan QR Code anggota ke kamera...'))
                    .catch((err) => {
                        setReaderHint('Izin kamera ditolak. Gunakan input NIM manual.');
                        console.error('Scanner error:', err);
                    });
            }

            document.addEventListener('livewire:init', () => {
                startScanner();
            });

            window.addEventListener('beforeunload', () => {
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.stop().catch(() => {});
                }
            });
        </script>
    @endpush

</x-app-layout>
