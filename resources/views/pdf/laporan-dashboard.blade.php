<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Statistik Absensi Bulanan</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { margin: 0; padding: 24px; color: #111827; }
        .kop table { width: 100%; border-collapse: collapse; }
        .kop .col { vertical-align: middle; }
        .kop .logo { width: 72px; height: 72px; object-fit: contain; }
        .kop-ukm { font-size: 11px; font-weight: bold; letter-spacing: 1px; }
        .kop-nama { font-size: 17px; font-weight: bold; color: #b91c1c; }
        .kop-univ { font-size: 12px; font-weight: bold; }
        .kop-alamat { font-size: 9px; font-style: italic; color: #374151; }
        hr.kop-line { border: none; border-top: 3px solid #000; margin: 12px 0 20px; }
        h1.title { font-size: 14px; text-align: center; margin: 0 0 4px; text-transform: uppercase; }
        .subtitle { font-size: 10px; text-align: center; color: #6b7280; margin-bottom: 18px; }
        h2.sec { font-size: 12px; margin: 18px 0 8px; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; }
        table.data { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.data th, table.data td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; }
        table.data th { background: #eef2ff; color: #374151; text-transform: uppercase; font-size: 9px; letter-spacing: 0.5px; }
        table.data td.num, table.data th.num { text-align: center; }
        .ringkasan { width: 100%; font-size: 11px; }
        .ringkasan td { padding: 4px 0; }
        .ringkasan .val { font-weight: bold; }
        .footer { margin-top: 24px; font-size: 9px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>
    @php
        $mimeOf = function ($path) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            return ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png';
        };
        $logoKiri = $settings->logo_unit_kegiatan && file_exists(public_path($settings->logo_unit_kegiatan))
            ? 'data:'.$mimeOf($settings->logo_unit_kegiatan).';base64,'.base64_encode(file_get_contents(public_path($settings->logo_unit_kegiatan)))
            : null;
        $logoKanan = $settings->logo_universitas && file_exists(public_path($settings->logo_universitas))
            ? 'data:'.$mimeOf($settings->logo_universitas).';base64,'.base64_encode(file_get_contents(public_path($settings->logo_universitas)))
            : null;
    @endphp

    <!-- Kop Surat (Letterhead) -->
    <div class="kop">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td class="col" width="20%" align="center">
                    @if ($logoKiri)
                        <img src="{{ $logoKiri }}" class="logo" alt="Logo Unit">
                    @endif
                </td>
                <td class="col" width="60%" align="center">
                    <div class="kop-ukm">UNIT KEGIATAN MAHASISWA</div>
                    <div class="kop-nama">{{ strtoupper($settings->nama_unit_kegiatan ?? 'UKM Taekwondo') }}</div>
                    <div class="kop-univ">{{ strtoupper($settings->nama_universitas ?? '') }}</div>
                    <div class="kop-alamat">{{ $settings->alamat_sekretariat ?? '' }}</div>
                </td>
                <td class="col" width="20%" align="center">
                    @if ($logoKanan)
                        <img src="{{ $logoKanan }}" class="logo" alt="Logo Universitas">
                    @endif
                </td>
            </tr>
        </table>
        <hr class="kop-line">
    </div>

    <h1 class="title">Laporan Statistik Absensi Bulanan</h1>
    <div class="subtitle">Periode: {{ $bulanLabel }}</div>

    <!-- Section 1: Ringkasan -->
    <h2 class="sec">Ringkasan</h2>
    <table class="ringkasan">
        <tr>
            <td width="50%">Total Hari Latihan</td>
            <td class="val">{{ $totalLatihan }} sesi</td>
        </tr>
        <tr>
            <td width="50%">Total Hari Libur</td>
            <td class="val">{{ $totalLibur }} hari</td>
        </tr>
    </table>

    <!-- Section 2: Top 3 Terajin -->
    <h2 class="sec">Anggota Terajin (Top 3)</h2>
    <table class="data">
        <thead>
            <tr>
                <th class="num" style="width:30px;">No</th>
                <th>Nama Anggota</th>
                <th style="width:90px;">NIM</th>
                <th class="num" style="width:110px;">Total Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topTerajin as $i => $row)
                <tr>
                    <td class="num">{{ $i + 1 }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $row['nim'] }}</td>
                    <td class="num">{{ $row['total'] }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center; color:#6b7280;">Tidak ada data kehadiran bulan ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Section 3: Paling Sering Alfa -->
    <h2 class="sec">Paling Sering Alfa (Top 5)</h2>
    <table class="data">
        <thead>
            <tr>
                <th class="num" style="width:30px;">No</th>
                <th>Nama Anggota</th>
                <th style="width:90px;">NIM</th>
                <th class="num" style="width:110px;">Total Alfa</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topAlfa as $i => $row)
                <tr>
                    <td class="num">{{ $i + 1 }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $row['nim'] }}</td>
                    <td class="num">{{ $row['total'] }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center; color:#6b7280;">Tidak ada data alfa bulan ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dokumen ini dihasilkan otomatis oleh Sistem Absensi UKM Taekwondo.</div>
</body>
</html>
