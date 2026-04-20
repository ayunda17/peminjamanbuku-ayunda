@extends('layouts.app')

@section('title', 'Pengembalian Buku')

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">✅ Pengembalian Buku</h5>
                </div>
                <div class="card-body">
                    <!-- Informasi Peminjaman -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Buku</h6>
                            <p class="mb-0"><strong>{{ $loan->book->title }}</strong></p>
                            <small class="text-muted">{{ $loan->book->author }}</small>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Anggota</h6>
                            <p class="mb-0"><strong>{{ $loan->member->name }}</strong></p>
                            <small class="text-muted">{{ $loan->member->phone }}</small>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Tanggal Peminjaman</h6>
                            <p class="mb-0">{{ $loan->loan_date->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Batas Pengembalian</h6>
                            <p class="mb-0 {{ now()->format('Y-m-d') > $loan->due_date->format('Y-m-d') ? 'text-danger' : '' }}">
                                {{ $loan->due_date->format('d M Y') }}
                                @if (now()->format('Y-m-d') > $loan->due_date->format('Y-m-d'))
                                    <span class="badge bg-danger">TERLAMBAT</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr>

                    <!-- Form Pengembalian -->
                    <form action="{{ route('loans.processReturn', $loan) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="return_date" class="form-label">Tanggal Pengembalian <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('return_date') is-invalid @enderror"
                                id="return_date" name="return_date" value="{{ date('Y-m-d') }}" required>
                            @error('return_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kalkulasi Denda -->
                        <div class="alert alert-warning" id="fineAlert" style="display: none;">
                            <h6 class="alert-heading">⚠️ Denda Keterlambatan</h6>
                            <p class="mb-0">Jika buku dikembalikan setelah batas, maka akan dikenakan denda:</p>
                            <p class="mb-0"><strong id="fineAmount">Rp 0</strong> (Rp 2.000 per hari)</p>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">Catat Pengembalian</button>
                            <a href="{{ route('loans.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const dueDate = new Date('{{ $loan->due_date->format('Y-m-d') }}');
        const returnDateInput = document.getElementById('return_date');
        const fineAlert = document.getElementById('fineAlert');
        const fineAmount = document.getElementById('fineAmount');

        function calculateFine() {
            const returnDate = new Date(returnDateInput.value);
            
            if (returnDate > dueDate) {
                const diffTime = returnDate - dueDate;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const fine = diffDays * 2000;
                
                fineAmount.textContent = 'Rp ' + fine.toLocaleString('id-ID');
                fineAlert.style.display = 'block';
            } else {
                fineAlert.style.display = 'none';
            }
        }

        returnDateInput.addEventListener('change', calculateFine);
        calculateFine(); // Initial calculation
    </script>
@endsection
