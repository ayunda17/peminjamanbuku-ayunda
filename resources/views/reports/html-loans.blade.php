<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman Buku</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            min-height: 100vh;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 24px;
            color: #1e3a5f;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 12px;
            margin: 3px 0;
        }

        /* Filter Info */
        .filter-info {
            background: #f0f9ff;
            border-left: 4px solid #0fa9e6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 13px;
        }

        .filter-info strong {
            color: #1e40af;
        }

        /* Print buttons -->
        .print-controls {
            text-align: center;
            margin-bottom: 20px;
            gap: 10px;
        }

        .btn-print {
            background: #2563eb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
            transition: background 0.3s;
        }

        .btn-print:hover {
            background: #1d4ed8;
        }

        .btn-back {
            background: #6b7280;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            background: #4b5563;
        }

        /* Summary stats -->
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .summary-item {
            background: #f8f6f1;
            padding: 15px;
            border-left: 4px solid #0d9488;
            border-radius: 5px;
        }

        .summary-item .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
        }

        .summary-item .value {
            font-size: 24px;
            font-weight: 700;
            color: #0d9488;
            margin-top: 5px;
        }

        /* Table -->
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        thead {
            background: #1e3a5f;
            color: white;
        }

        thead th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        tbody tr:nth-child(even) {
            background: #f8f6f1;
        }

        tbody tr:hover {
            background: #f0f9ff;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-aktif {
            background: #d1fae5;
            color: #065f46;
        }

        .status-terlambat {
            background: #fee2e2;
            color: #7f1d1d;
        }

        .status-dikembalikan {
            background: #e5e7eb;
            color: #374151;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }

        /* Print styles */
        @media print {
            body {
                background: white;
            }

            .container {
                padding: 0;
                max-width: 100%;
                background: white;
            }

            .print-controls {
                display: none;
            }

            .btn-back {
                display: none;
            }

            table {
                box-shadow: none;
                page-break-inside: avoid;
            }

            tbody tr {
                page-break-inside: avoid;
            }
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .no-data-icon {
            font-size: 48px;
            margin-bottom: 10px;
            color: #ccc;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📚 LAPORAN PEMINJAMAN BUKU 📚</h1>
            <p>Perpustakaan Sekolah</p>
            <p>Jl. Pendidikan No. 123, Kota Besar</p>
        </div>

        <!-- Print Controls -->
        <div class="print-controls">
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak / Print
            </button>
            <a href="{{ route('reports.index', request()->query()) }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <!-- Filter Info -->
        @if(request('start_date') || request('end_date'))
        <div class="filter-info">
            <strong><i class="fas fa-filter"></i> Filter Periode:</strong>
            @if(request('start_date'))
                Dari: {{ \Carbon\Carbon::parse(request('start_date'))->format('d M Y') }}
            @endif
            @if(request('end_date'))
                s/d: {{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}
            @endif
            | Dicetak: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}
        </div>
        @else
        <div class="filter-info">
            <strong><i class="fas fa-filter"></i> Filter:</strong> Semua Data | Dicetak: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}
        </div>
        @endif

        <!-- Summary Stats -->
        @if(!$loans->isEmpty())
        <div class="summary">
            <div class="summary-item">
                <div class="label"><i class="fas fa-list"></i> Total Peminjaman</div>
                <div class="value">{{ $loans->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label"><i class="fas fa-book-open"></i> Masih Dipinjam</div>
                <div class="value">{{ $loans->where('return_date', null)->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label"><i class="fas fa-exclamation-circle"></i> Terlambat</div>
                <div class="value">{{ $loans->filter(fn($l) => $l->isOverdue())->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label"><i class="fas fa-money-bill"></i> Total Denda</div>
                <div class="value">Rp {{ number_format($totalFine, 0, ',', '.') }}</div>
            </div>
        </div>
        @endif

        <!-- Table -->
        @if($loans->isEmpty())
            <div class="no-data">
                <div class="no-data-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <p>Tidak ada data peminjaman untuk filter yang dipilih</p>
            </div>
        @else
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 18%;">Buku</th>
                    <th style="width: 14%;">Anggota</th>
                    <th style="width: 14%;">Penanggung Jawab</th>
                    <th style="width: 12%;">Tgl Pinjam</th>
                    <th style="width: 12%;">Batas Kembali</th>
                    <th style="width: 12%;">Tgl Kembali</th>
                    <th style="width: 11%;">Status</th>
                    <th style="width: 12%;" class="text-right">Denda</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loans as $loan)
                    <tr>
                        <td class="text-center" style="font-weight: 600;">{{ $loop->iteration }}</td>
                        <td>
                            <strong style="color: #1e3a5f;">{{ $loan->book->title }}</strong>
                            <br>
                            <small style="color: #666;">{{ $loan->book->author ?? '-' }}</small>
                        </td>
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
                                <span class="status-badge {{ $loan->isOverdue() ? 'status-terlambat' : 'status-aktif' }}">
                                    {{ $loan->isOverdue() ? 'Terlambat' : 'Aktif' }}
                                </span>
                            @else
                                <span class="status-badge status-dikembalikan">Dikembalikan</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @if($loan->fine > 0)
                                <strong style="color: #dc2626;">Rp {{ number_format($loan->fine, 0, '.', '.') }}</strong>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Laporan ini dihasilkan secara otomatis oleh Sistem Manajemen Perpustakaan</p>
            <p>© {{ now()->year }} - Semua hak dilindungi</p>
        </div>
    </div>

    <script>
        // Prevent accidental navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                history.back();
            }
        });
    </script>
</body>
</html>
