@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="glass-card">
            <div class="card-header d-flex justify-content-between">
                <h4>Detail User</h4>
                <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Nama</label>
                    <div class="col-sm-9">
                        <strong>{{ $user->name }}</strong>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">NIS</label>
                    <div class="col-sm-9">{{ $user->nis ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Email</label>
                    <div class="col-sm-9">{{ $user->email }}</div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Role</label>
                    <div class="col-sm-9">{{ $user->role ?? 'user' }}</div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Status</label>
                    <div class="col-sm-9">
                        @if($user->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Dibuat</label>
                    <div class="col-sm-9">{{ $user->created_at->format('d M Y H:i') }}</div>
                </div>
                
                @if(!$user->is_active)
                <div class="row">
                    <label class="col-sm-3 col-form-label"></label>
                    <div class="col-sm-9">
                        <div class="d-flex gap-2">
                            <form action="{{ route('users.approve', $user) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-success btn-modern" onclick="return confirm('Setujui user ini?')">
                                    <i class="fas fa-check"></i> Setujui
                                </button>
                            </form>
                            <form action="{{ route('users.reject', $user) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-danger btn-modern" onclick="return confirm('Tolak user ini?')">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

