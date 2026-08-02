<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Kehadiran</title>
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
        h1 { font-size: 16px; margin: 0 0 4px; }
        .meta { font-size: 11px; color: #6b7280; margin-bottom: 16px; }
        .meta span { color: #111827; font-weight: bold; }
        table.data { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.data th, table.data td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; }
        table.data th { background: #eef2ff; color: #374151; text-transform: uppercase; font-size: 9px; letter-spacing: 0.5px; }
        table.data td.num, table.data th.num { text-align: center; }
        .footer { margin-top: 18px; font-size: 9px; color: #9ca3af; text-align: right; }
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

    <h1>Rekap Kehadiran Anggota</h1>
    <div class="meta">
        Periode: <span>{{ \Carbon\Carbon::parse($start)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($end)->translatedFormat('d M Y') }}</span>
        @if ($search !== '')
            &nbsp;·&nbsp; Filter: <span>{{ $search }}</span>
        @endif
    </div>

    <table class="data">
        <thead>
            <tr>
                <th class="num" style="width:30px;">No</th>
                <th>Nama Anggota</th>
                <th style="width:90px;">NIM</th>
                <th class="num" style="width:70px;">Total Sakit</th>
                <th class="num" style="width:70px;">Total Izin</th>
                <th class="num" style="width:70px;">Total Alfa</th>
                <th class="num" style="width:70px;">Total Hadir</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($anggota as $a)
                <tr>
                    <td class="num">{{ $loop->index + 1 }}</td>
                    <td>{{ $a->nama_lengkap }}</td>
                    <td>{{ $a->nim }}</td>
                    <td class="num">{{ $a->total_sakit }}</td>
                    <td class="num">{{ $a->total_izin }}</td>
                    <td class="num">{{ $a->total_alfa }}</td>
                    <td class="num">{{ $a->total_hadir }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; color:#6b7280;">Tidak ada data ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total: Hadir {{ $summary['hadir'] }} · Sakit {{ $summary['sakit'] }} · Izin {{ $summary['izin'] }} · Alfa {{ $summary['alfa'] }}
    </div>
</body>
</html>
