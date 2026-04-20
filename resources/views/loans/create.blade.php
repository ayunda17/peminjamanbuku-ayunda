@extends('layouts.app')

@section('title', 'Peminjaman Buku Baru')

@section('content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📚 Peminjaman Buku Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('loans.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="book_id" class="form-label">Pilih Buku <span class="text-danger">*</span></label>
                            <select class="form-select @error('book_id') is-invalid @enderror" id="book_id" name="book_id"
                                required>
                                <option value="">-- Pilih Buku --</option>
                                @foreach ($books as $book)
                                    <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                        {{ $book->title }} (Stok: {{ $book->stock }})
                                    </option>
                                @endforeach
                            </select>
                            @error('book_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="member_id" class="form-label">Pilih Anggota <span class="text-danger">*</span></label>
                            <select class="form-select @error('member_id') is-invalid @enderror" id="member_id"
                                name="member_id" required>
                                <option value="">-- Pilih Anggota --</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('member_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="penanggung_jawab_id" class="form-label">Pilih Penanggung Jawab <span class="text-danger">*</span></label>
                            <select class="form-select @error('penanggung_jawab_id') is-invalid @enderror" id="penanggung_jawab_id"
                                name="penanggung_jawab_id" required>
                                <option value="">-- Pilih Penanggung Jawab --</option>
                                @foreach ($penanggungJawabs as $pj)
                                    <option value="{{ $pj->id }}" {{ old('penanggung_jawab_id') == $pj->id ? 'selected' : '' }}>
                                        {{ $pj->nama }} - {{ $pj->jabatan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('penanggung_jawab_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="loan_date" class="form-label">Tanggal Peminjaman <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('loan_date') is-invalid @enderror"
                                id="loan_date" name="loan_date" value="{{ old('loan_date', date('Y-m-d')) }}" 
                                min="{{ date('Y-m-d') }}" required>
                            <small class="text-muted d-block mt-1">
                                ℹ️ Peminjaman hanya dapat dilakukan mulai hari ini atau tanggal ke depan.
                            </small>
                            @error('loan_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">Batas Pengembalian <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date"
                                name="due_date" value="{{ old('due_date') }}" readonly>
                            <small class="text-muted d-block mt-1">
                                <strong>Dihitung otomatis:</strong> Tanggal peminjaman + 3 hari
                            </small>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info mb-3">
                            💡 <strong>Catatan:</strong> Batas pengembalian adalah tanggal peminjaman + 3 hari.
                            Denda keterlambatan Rp 2.000 per hari setelah batas pengembalian.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Catat Peminjaman</button>
                            <a href="{{ route('loans.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        const loanDateInput = document.getElementById('loan_date');
        loanDateInput.setAttribute('min', today);
        
        // Jika nilai awal adalah tanggal lama, reset ke hari ini
        const currentValue = loanDateInput.value;
        if (currentValue && currentValue < today) {
            loanDateInput.value = today;
            updateDueDate();
        } else if (!currentValue) {
            loanDateInput.value = today;
            updateDueDate();
        }

        // Auto-set batas pengembalian (3 hari setelah tanggal pinjam)
        loanDateInput.addEventListener('change', function() {
            updateDueDate();
        });

        function updateDueDate() {
            const loanDateValue = document.getElementById('loan_date').value;
            
            if (loanDateValue) {
                const loanDate = new Date(loanDateValue + 'T00:00:00'); // Parse sebagai local date
                const dueDate = new Date(loanDate);
                dueDate.setDate(dueDate.getDate() + 3); // 3 hari kemudian

                const dueDateStr = dueDate.toISOString().split('T')[0];
                document.getElementById('due_date').value = dueDateStr;
            }
        }

        // Initialize due date on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateDueDate();
        });
    </script>
@endsection
