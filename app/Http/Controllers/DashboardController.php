<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Book;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Redirect based on user role
        if (auth()->user()->isAdmin()) {
            return $this->adminDashboard();
        } else {
            return $this->memberDashboard();
        }
    }

    private function adminDashboard()
    {
        // Notifikasi untuk admin
        $notifications = [];

        // 1. Peminjaman yang jatuh tempo besok
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $dueTomorrow = Loan::where('due_date', $tomorrow)
            ->whereNull('return_date')
            ->with('book', 'member')
            ->get();

        if ($dueTomorrow->count() > 0) {
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'fas fa-clock',
                'title' => 'Peminjaman Jatuh Tempo Besok',
                'message' => "Ada {$dueTomorrow->count()} peminjaman yang jatuh tempo besok.",
                'count' => $dueTomorrow->count()
            ];
        }

        // 2. Stok buku menipis (< 5)
        $lowStockBooks = Book::where('stock', '<', 5)
            ->where('stock', '>', 0)
            ->get();

        if ($lowStockBooks->count() > 0) {
            $notifications[] = [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-triangle',
                'title' => 'Stok Buku Menipis',
                'message' => "Ada {$lowStockBooks->count()} buku dengan stok rendah (< 5).",
                'count' => $lowStockBooks->count()
            ];
        }

        return view('dashboard', compact('notifications'));
    }

    private function memberDashboard()
    {
        // Notifikasi untuk member
        $notifications = [];

        // 1. Peminjaman member yang jatuh tempo besok
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $dueTomorrow = Loan::where('member_id', auth()->id())
            ->where('due_date', $tomorrow)
            ->whereNull('return_date')
            ->with('book')
            ->get();

        if ($dueTomorrow->count() > 0) {
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'fas fa-clock',
                'title' => 'Buku Jatuh Tempo Besok',
                'message' => "Ada {$dueTomorrow->count()} buku yang harus dikembalikan besok.",
                'count' => $dueTomorrow->count()
            ];
        }

        // 2. Peminjaman yang sudah terlambat
        $overdueLoans = Loan::where('member_id', auth()->id())
            ->where('due_date', '<', Carbon::today()->format('Y-m-d'))
            ->whereNull('return_date')
            ->with('book')
            ->get();

        if ($overdueLoans->count() > 0) {
            $notifications[] = [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-circle',
                'title' => 'Buku Terlambat Dikembalikan',
                'message' => "Ada {$overdueLoans->count()} buku yang sudah terlambat.",
                'count' => $overdueLoans->count()
            ];
        }

        return view('dashboard-member', compact('notifications'));
    }
}
