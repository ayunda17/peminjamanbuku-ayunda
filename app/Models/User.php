<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nis',
        'email',
        'password',
        'address',
        'phone',
        'is_active',
        'pending_status',
        'role',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'role' => 'string',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the name of the unique identifier for the user (NIS as username).
     */
    public function getAuthIdentifierName()
    {
        return 'email';
    }

    /**
     * Scope untuk user aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk pending approval
     */
    public function scopePending($query)
    {
        return $query->where('pending_status', 'pending');
    }

    /**
     * Relasi: User memiliki satu Member berdasarkan NIS
     */
    public function member()
    {
        return $this->hasOne(Member::class, 'nis', 'nis');
    }

    /**
     * Dapatkan peminjaman aktif user
     */
    public function getActiveLoans()
    {
        if (!$this->member) {
            return collect();
        }
        return Loan::where('member_id', $this->member->id)
            ->whereNull('return_date')
            ->with('book', 'member')
            ->get();
    }

    /**
     * Dapatkan semua peminjaman user
     */
    public function getAllLoans()
    {
        if (!$this->member) {
            return collect();
        }
        return Loan::where('member_id', $this->member->id)
            ->with('book', 'member')
            ->get();
    }

    /**
     * Dapatkan riwayat peminjaman (yang sudah dikembalikan)
     */
    public function getLoanHistory()
    {
        if (!$this->member) {
            return collect();
        }
        return Loan::where('member_id', $this->member->id)
            ->whereNotNull('return_date')
            ->with('book', 'member')
            ->orderBy('return_date', 'desc')
            ->get();
    }

    /**
     * Hitung total denda yang belum dibayar
     */
    public function getTotalUnpaidFines()
    {
        if (!$this->member) {
            return 0;
        }
        return Loan::where('member_id', $this->member->id)
            ->whereNotNull('return_date')
            ->where('fine', '>', 0)
            ->sum('fine');
    }

    /**
     * Dapatkan peminjaman dengan denda yang belum dibayar
     */
    public function getUnpaidFines()
    {
        if (!$this->member) {
            return collect();
        }
        return Loan::where('member_id', $this->member->id)
            ->where('fine', '>', 0)
            ->with('book', 'member')
            ->get();
    }

    /**
     * Cek apakah user adalah member (bukan admin)
     */
    public function isMember()
    {
        return $this->role === 'member' || $this->role !== 'admin';
    }

    /**
     * Cek apakah user admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
