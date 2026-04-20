<?php

/**
 * CONTOH IMPLEMENTASI PAGINASI
 * 
 * File ini menunjukkan beberapa skenario penggunaan paginasi
 * dalam aplikasi PHP (baik Laravel maupun Plain PHP)
 */

// ============================================================
// CONTOH 1: Menggunakan PaginationService di Controller (Laravel)
// ============================================================

/*
use App\Services\PaginationService;
use App\Models\Book;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        
        // Build query
        $query = Book::query();
        
        // Filter
        if (!empty($search)) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
        }
        
        // Pagination setup
        $total = $query->count();
        $pagination = new PaginationService($page, 10);
        $pagination->setTotal($total);
        
        // Get data
        $books = $query->offset($pagination->getOffset())
                       ->limit($pagination->getPerPage())
                       ->get();
        
        // Pass to view
        return view('books.index', [
            'books' => $books,
            'pagination' => $pagination->toArray(),
            'search' => $search,
        ]);
    }
}
*/

// ============================================================
// CONTOH 2: Helper Functions (Bisa di-include di mana saja)
// ============================================================

/*
// Include helper file
include_once 'app/Helpers/PaginationHelper.php';

// Get pagination info
$paginationInfo = getPaginationInfo(2, 10, 25);
// Output:
// [
//     'currentPage' => 2,
//     'totalPages' => 3,
//     'totalItems' => 25,
//     'offset' => 10,
//     'firstItem' => 11,
//     'lastItem' => 20,
//     'hasPrevious' => true,
//     'hasNext' => true,
// ]

// Generate HTML pagination
$html = generatePaginationHTML(2, 3, 'books.php?', 2);
echo $html;

// Get info text
echo getPaginationText(11, 20, 25, 2, 3);
// Output: "Menampilkan 11-20 dari 25 data | Halaman 2 dari 3"

// Get LIMIT clause
echo getLimitClause(2, 10);  // "LIMIT 10 OFFSET 10"

// Get LIMIT & OFFSET array
$limits = getLimitOffset(2, 10);
// ['limit' => 10, 'offset' => 10]
*/

// ============================================================
// CONTOH 3: Plain PHP (Tanpa Laravel)
// ============================================================

/*
// database-paginasi.php
<?php
include 'app/Helpers/PaginationHelper.php';

// Koneksi database (contoh: MySQLi)
$mysqli = new mysqli("localhost", "user", "password", "peminjaman_buku");

// Get parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$where = "";
if (!empty($search)) {
    $search = $mysqli->real_escape_string($search);
    $where = "WHERE title LIKE '%$search%' OR author LIKE '%$search%'";
}

// Count total
$totalResult = $mysqli->query("SELECT COUNT(*) FROM books $where");
$totalItems = $totalResult->fetch_row()[0];

// Setup pagination
$limits = getLimitOffset($page, 10);

// Get data
$sql = "SELECT * FROM books $where LIMIT {$limits['limit']} OFFSET {$limits['offset']}";
$result = $mysqli->query($sql);

// Calculate pages
$totalPages = ceil($totalItems / 10);

// Render
?>
<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>📚 Daftar Buku</h1>
        
        <!-- Search Form -->
        <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari buku..." value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-primary" type="submit">Cari</button>
            </div>
        </form>
        
        <!-- Info Text -->
        <div class="mb-3 text-muted">
            <?php 
            $info = getPaginationInfo($page, 10, $totalItems);
            echo getPaginationText($info['firstItem'], $info['lastItem'], 
                                    $totalItems, $page, $totalPages); 
            ?>
        </div>
        
        <!-- Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Penerbit</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = $info['firstItem'];
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['author']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['publisher']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        
        <!-- Pagination HTML -->
        <?php
        $baseUrl = "database-paginasi.php?";
        if (!empty($search)) {
            $baseUrl .= "search=" . urlencode($search) . "&";
        }
        echo generatePaginationHTML($page, $totalPages, $baseUrl, 2);
        ?>
    </div>
</body>
</html>
*/

// ============================================================
// CONTOH 4: Pagination dengan Filter (Laravel)
// ============================================================

/*
class LoanController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        
        // Build query dengan relasi
        $query = Loan::with('book', 'member', 'penanggungJawab');
        
        // Filter search
        if (!empty($search)) {
            $query->whereHas('book', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            })->orWhereHas('member', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        // Filter status
        if ($status === 'active') {
            $query->whereNull('return_date');
        } elseif ($status === 'overdue') {
            $query->whereNull('return_date')
                  ->whereDate('due_date', '<', now());
        } elseif ($status === 'returned') {
            $query->whereNotNull('return_date');
        }
        
        // Pagination
        $total = $query->count();
        $pagination = new PaginationService($page, 10);
        $pagination->setTotal($total);
        
        $loans = $query->orderBy('loan_date', 'desc')
                       ->offset($pagination->getOffset())
                       ->limit($pagination->getPerPage())
                       ->get();
        
        return view('loans.index', [
            'loans' => $loans,
            'pagination' => $pagination->toArray(),
            'search' => $search,
            'status' => $status,
        ]);
    }
}
*/

// ============================================================
// CONTOH 5: Pagination di View (Blade)
// ============================================================

/*
<!-- resources/views/books/index.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Buku</h1>
    
    <!-- Search Form -->
    <form method="GET" action="{{ route('books.index') }}" class="mb-3">
        <div class="row">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari judul, pengarang..." 
                       value="{{ $search }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Cari</button>
            </div>
        </div>
    </form>
    
    @if($books->isEmpty())
        <div class="alert alert-info">
            @if(!empty($search))
                Tidak ada hasil pencarian
            @else
                Belum ada buku
            @endif
        </div>
    @else
        <!-- Info text -->
        <div class="mb-3 text-muted">
            Menampilkan <strong>{{ $pagination['firstItem'] }}-{{ $pagination['lastItem'] }}</strong>
            dari <strong>{{ $pagination['totalItems'] }}</strong> data
            | Halaman <strong>{{ $pagination['currentPage'] }}</strong> dari <strong>{{ $pagination['totalPages'] }}</strong>
        </div>
        
        <!-- Table -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Penerbit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($books as $book)
                    <tr>
                        <!-- ✅ Gunakan pagination firstItem + loop index -->
                        <td>{{ $pagination['firstItem'] + $loop->index }}</td>
                        <td>{{ $book->title }}</td>
                        <td>{{ $book->author }}</td>
                        <td>{{ $book->publisher }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Pagination Component -->
        <x-pagination-component 
            :pagination="$pagination"
            routeName="books.index"
            :queryParams="['search' => $search]"
        />
    @endif
</div>
@endsection
*/

// ============================================================
// CONTOH 6: Integrasi dengan Chart/Dashboard
// ============================================================

/*
class DashboardController extends Controller
{
    public function index()
    {
        $pagination = new PaginationService(1, 10);
        
        // Get recent loans (hanya 10)
        $recentLoans = Loan::with('book', 'member')
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get();
        
        $pagination->setTotal($recentLoans->count());
        
        return view('dashboard', [
            'recentLoans' => $recentLoans,
            'paginationInfo' => $pagination->toArray(),
        ]);
    }
}
*/

// ============================================================
// CONTOH 7: API Response dengan Pagination
// ============================================================

/*
class BookApiController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        
        $query = Book::query();
        $total = $query->count();
        
        $pagination = new PaginationService($page, $perPage);
        $pagination->setTotal($total);
        
        $books = $query->offset($pagination->getOffset())
                       ->limit($pagination->getPerPage())
                       ->get();
        
        return response()->json([
            'success' => true,
            'data' => $books,
            'pagination' => $pagination->toArray(),
        ]);
    }
}

// Response:
// {
//     "success": true,
//     "data": [...],
//     "pagination": {
//         "currentPage": 1,
//         "perPage": 10,
//         "totalItems": 25,
//         "totalPages": 3,
//         "offset": 0,
//         "firstItem": 1,
//         "lastItem": 10,
//         ...
//     }
// }
*/

?>

<!-- DOKUMENTASI LENGKAP TERSEDIA DI: PAGINATION_DOCUMENTATION.md -->
