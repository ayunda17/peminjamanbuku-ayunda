<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'member_id',
        'penanggung_jawab_id',
        'loan_date',
        'return_date',
        'due_date',
        'fine',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'return_date' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Relasi: Loan milik satu Book
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Relasi: Loan milik satu Member
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Relasi: Loan milik satu Penanggung Jawab
     */
    public function penanggungJawab()
    {
        return $this->belongsTo(PenanggungJawab::class, 'penanggung_jawab_id');
    }

    /**
     * Hitung denda otomatis (Rp 2.000 per hari keterlambatan)
     */
    public function calculateFine()
    {
        if ($this->return_date && $this->due_date) {
            $returnDateStr = $this->return_date->format('Y-m-d');
            $dueDateStr = $this->due_date->format('Y-m-d');

            // Bandingkan tanggal menggunakan strtotime
            $returnTimestamp = strtotime($returnDateStr);
            $dueTimestamp = strtotime($dueDateStr);

            if ($returnTimestamp > $dueTimestamp) {
                $diffSeconds = $returnTimestamp - $dueTimestamp;
                $lateDays = floor($diffSeconds / (60 * 60 * 24));
                return $lateDays * 2000; // Rp 2.000 per hari
            }
        }
        return 0;
    }

    /**
     * Cek apakah peminjaman masih aktif (belum dikembalikan)
     */
    public function isActive()
    {
        return is_null($this->return_date);
    }

    /**
     * Cek apakah peminjaman terlambat
     */
    public function isOverdue()
    {
        if ($this->isActive()) {
            return Carbon::now()->greaterThan($this->due_date);
        }
        return false;
    }
}
