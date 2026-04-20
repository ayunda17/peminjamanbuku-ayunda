<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            width: 100%;
            padding: 20px;
        }

        /* Kop Surat */
        .header {
            text-align: center;
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            color: #1e3a5f;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            color: #666;
            margin: 3px 0;
        }

        /* Filter Info */
        .filter-info {
            background-color: #f8f6f1;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #0d9488;
            font-size: 11px;
        }

        .filter-info p {
            margin: 3px 0;
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead th {
            background-color: #1e3a5f;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            border: 1px solid #1e3a5f;
        }

        tbody td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            font-size: 11px;
            vertical-align: top;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f6f1;
        }

        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        /* Status Badge */
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-aktif {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-terlambat {
            background-color: #fee2e2;
            color: #7f1d1d;
        }

        .badge-dikembalikan {
            background-color: #e5e7eb;
            color: #374151;
        }

        /* Text alignment */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* Summary */
        .summary {
            background-color: #f0f9ff;
            padding: 10px;
            border-left: 4px solid #0d9488;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .summary-label {
            font-weight: bold;
        }

        .summary-value {
            color: #0d9488;
            font-weight: bold;
        }

        /* Page break */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header / Kop Surat -->
        <div class="header">
            <h1>📚 LAPORAN PEMINJAMAN BUKU 📚</h1>
            <p>Perpustakaan Sekolah</p>
            <p>Jl. Pendidikan No. 123, Kota Besar</p>
        </div>

        <!-- Filter Information -->
        @if($startDate || $endDate)
        <div class="filter-info">
            <strong>Filter Periode:</strong>
            @if($startDate)
                Dari: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
            @endif
            @if($endDate)
                s/d: {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            @endif
            | Laporan dibuat: {{ $generatedAt->format('d M Y H:i') }}
        </div>
        @else
        <div class="filter-info">
            <strong>Filter:</strong> Semua Data | Laporan dibuat: {{ $generatedAt->format('d M Y H:i') }}
        </div>
        @endif

        <!-- Summary -->
        <div class="summary">
            <div class="summary-row">
                <span class="summary-label">Total Peminjaman:</span>
                <span class="summary-value">{{ $loans->count() }} records</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Peminjaman Aktif:</span>
                <span class="summary-value">{{ $loans->where('return_date', null)->count() }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Peminjaman Terlambat:</span>
                <span class="summary-value">{{ $loans->filter(fn($l) => $l->isOverdue())->count() }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Total Denda Tertunggak:</span>
                <span class="summary-value">Rp {{ number_format($totalFine, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 16%;">Buku</th>
                    <th style="width: 10%;">Author</th>
                    <th style="width: 14%;">Anggota</th>
                    <th style="width: 14%;">Penanggung Jawab</th>
                    <th style="width: 10%;">Tgl Pinjam</th>
                    <th style="width: 10%;">Batas Kembali</th>
                    <th style="width: 10%;">Tgl Kembali</th>
                    <th style="width: 11%;">Status</th>
                    <th style="width: 10%; text-align: right;">Denda</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $loan)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $loan->book->title }}</td>
                        <td>{{ $loan->book->author ?? '-' }}</td>
                        <td>{{ $loan->member->name }}</td>
                        <td>{{ $loan->penanggungJawab?->nama ?? '-' }}</td>
                        <td class="text-center">{{ $loan->loan_date->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $loan->due_date->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @if($loan->return_date)
                                {{ $loan->return_date->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($loan->isActive())
                                @if($loan->isOverdue())
                                    <span class="badge badge-terlambat">Terlambat</span>
                                @else
                                    <span class="badge badge-aktif">Aktif</span>
                                @endif
                            @else
                                <span class="badge badge-dikembalikan">Dikembalikan</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @if($loan->fine > 0)
                                <strong>Rp {{ number_format($loan->fine, 0, '.', '.') }}</strong>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 20px;">
                            Tidak ada data peminjaman
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Laporan ini dihasilkan secara otomatis oleh Sistem Manajemen Perpustakaan</p>
            <p style="margin-top: 5px;">Dicetak pada: {{ $generatedAt->format('d M Y - H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
