<?php
// Contoh operasi CRUD yang aman dengan prepared statements

// 1. CREATE - Tambah Buku Baru
function addBook($title, $author, $isbn, $stock) {
    $conn = getDBConnection();

    try {
        // Validasi input
        $errors = validateInput([
            'title' => $title,
            'author' => $author,
            'isbn' => $isbn,
            'stock' => $stock
        ], [
            'title' => 'required|min:1|max:255',
            'author' => 'required|min:1|max:255',
            'isbn' => 'required|min:10|max:20',
            'stock' => 'required|numeric'
        ]);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Cek apakah ISBN sudah ada
        $stmt = $conn->prepare("SELECT id FROM books WHERE isbn = ?");
        $stmt->execute([$isbn]);
        if ($stmt->fetch()) {
            return ['success' => false, 'errors' => ['ISBN sudah terdaftar']];
        }

        // Insert dengan prepared statement
        $stmt = $conn->prepare("INSERT INTO books (title, author, isbn, stock, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$title, $author, $isbn, $stock]);

        logActivity('add_book', "Menambah buku: $title");
        return ['success' => true, 'message' => 'Buku berhasil ditambahkan'];

    } catch (PDOException $e) {
        error_log("Error adding book: " . $e->getMessage());
        return ['success' => false, 'errors' => ['Terjadi kesalahan sistem']];
    }
}

// 2. READ - Ambil Data Buku dengan Pagination
function getBooks($page = 1, $perPage = 10, $search = '') {
    $conn = getDBConnection();
    $offset = ($page - 1) * $perPage;

    try {
        // Hitung total records
        $countQuery = "SELECT COUNT(*) as total FROM books WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $countQuery .= " AND (title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
            $searchParam = "%$search%";
            $params = [$searchParam, $searchParam, $searchParam];
        }

        $stmt = $conn->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch()['total'];

        // Ambil data dengan pagination
        $query = "SELECT id, title, author, isbn, stock, created_at FROM books WHERE 1=1";

        if (!empty($search)) {
            $query .= " AND (title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
        }

        $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $books = $stmt->fetchAll();

        return [
            'success' => true,
            'books' => $books,
            'total' => $total,
            'pages' => ceil($total / $perPage),
            'current_page' => $page
        ];

    } catch (PDOException $e) {
        error_log("Error getting books: " . $e->getMessage());
        return ['success' => false, 'error' => 'Terjadi kesalahan sistem'];
    }
}

// 3. UPDATE - Edit Buku
function updateBook($id, $title, $author, $isbn, $stock) {
    $conn = getDBConnection();

    try {
        // Validasi input
        $errors = validateInput([
            'id' => $id,
            'title' => $title,
            'author' => $author,
            'isbn' => $isbn,
            'stock' => $stock
        ], [
            'id' => 'required|numeric',
            'title' => 'required|min:1|max:255',
            'author' => 'required|min:1|max:255',
            'isbn' => 'required|min:10|max:20',
            'stock' => 'required|numeric'
        ]);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Cek apakah buku ada
        $stmt = $conn->prepare("SELECT id FROM books WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            return ['success' => false, 'errors' => ['Buku tidak ditemukan']];
        }

        // Cek apakah ISBN sudah digunakan buku lain
        $stmt = $conn->prepare("SELECT id FROM books WHERE isbn = ? AND id != ?");
        $stmt->execute([$isbn, $id]);
        if ($stmt->fetch()) {
            return ['success' => false, 'errors' => ['ISBN sudah digunakan buku lain']];
        }

        // Update dengan prepared statement
        $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, isbn = ?, stock = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$title, $author, $isbn, $stock, $id]);

        logActivity('update_book', "Mengupdate buku ID: $id");
        return ['success' => true, 'message' => 'Buku berhasil diupdate'];

    } catch (PDOException $e) {
        error_log("Error updating book: " . $e->getMessage());
        return ['success' => false, 'errors' => ['Terjadi kesalahan sistem']];
    }
}

// 4. DELETE - Hapus Buku
function deleteBook($id) {
    $conn = getDBConnection();

    try {
        // Cek apakah buku ada
        $stmt = $conn->prepare("SELECT id, title FROM books WHERE id = ?");
        $stmt->execute([$id]);
        $book = $stmt->fetch();

        if (!$book) {
            return ['success' => false, 'errors' => ['Buku tidak ditemukan']];
        }

        // Cek apakah buku sedang dipinjam
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM loans WHERE book_id = ? AND return_date IS NULL");
        $stmt->execute([$id]);
        if ($stmt->fetch()['count'] > 0) {
            return ['success' => false, 'errors' => ['Buku sedang dipinjam, tidak dapat dihapus']];
        }

        // Delete dengan prepared statement
        $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$id]);

        logActivity('delete_book', "Menghapus buku: {$book['title']}");
        return ['success' => true, 'message' => 'Buku berhasil dihapus'];

    } catch (PDOException $e) {
        error_log("Error deleting book: " . $e->getMessage());
        return ['success' => false, 'errors' => ['Terjadi kesalahan sistem']];
    }
}

// 5. PEMINJAMAN BUKU dengan transaksi
function borrowBook($bookId, $memberId, $loanDate, $dueDate) {
    $conn = getDBConnection();

    try {
        $conn->beginTransaction();

        // Validasi input
        $errors = validateInput([
            'book_id' => $bookId,
            'member_id' => $memberId,
            'loan_date' => $loanDate,
            'due_date' => $dueDate
        ], [
            'book_id' => 'required|numeric',
            'member_id' => 'required|numeric',
            'loan_date' => 'required',
            'due_date' => 'required'
        ]);

        if (!empty($errors)) {
            $conn->rollBack();
            return ['success' => false, 'errors' => $errors];
        }

        // Cek stok buku
        $stmt = $conn->prepare("SELECT stock FROM books WHERE id = ?");
        $stmt->execute([$bookId]);
        $book = $stmt->fetch();

        if (!$book || $book['stock'] <= 0) {
            $conn->rollBack();
            return ['success' => false, 'errors' => ['Stok buku tidak tersedia']];
        }

        // Cek apakah member ada
        $stmt = $conn->prepare("SELECT id FROM members WHERE id = ?");
        $stmt->execute([$memberId]);
        if (!$stmt->fetch()) {
            $conn->rollBack();
            return ['success' => false, 'errors' => ['Anggota tidak ditemukan']];
        }

        // Insert peminjaman
        $stmt = $conn->prepare("INSERT INTO loans (book_id, member_id, loan_date, due_date, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$bookId, $memberId, $loanDate, $dueDate]);

        // Kurangi stok
        $stmt = $conn->prepare("UPDATE books SET stock = stock - 1 WHERE id = ?");
        $stmt->execute([$bookId]);

        $conn->commit();

        logActivity('borrow_book', "Peminjaman buku ID: $bookId oleh member ID: $memberId");
        return ['success' => true, 'message' => 'Peminjaman berhasil dicatat'];

    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Error borrowing book: " . $e->getMessage());
        return ['success' => false, 'errors' => ['Terjadi kesalahan sistem']];
    }
}

// 6. PENGEMBALIAN BUKU dengan perhitungan denda
function returnBook($loanId, $returnDate) {
    $conn = getDBConnection();

    try {
        $conn->beginTransaction();

        // Ambil data peminjaman
        $stmt = $conn->prepare("SELECT * FROM loans WHERE id = ?");
        $stmt->execute([$loanId]);
        $loan = $stmt->fetch();

        if (!$loan) {
            $conn->rollBack();
            return ['success' => false, 'errors' => ['Data peminjaman tidak ditemukan']];
        }

        if ($loan['return_date']) {
            $conn->rollBack();
            return ['success' => false, 'errors' => ['Buku sudah dikembalikan']];
        }

        // Hitung denda
        $dueDate = strtotime($loan['due_date']);
        $actualReturnDate = strtotime($returnDate);
        $fine = 0;

        if ($actualReturnDate > $dueDate) {
            $lateDays = floor(($actualReturnDate - $dueDate) / (60 * 60 * 24));
            $fine = $lateDays * 2000; // Rp 2.000 per hari
        }

        // Update peminjaman
        $stmt = $conn->prepare("UPDATE loans SET return_date = ?, fine = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$returnDate, $fine, $loanId]);

        // Tambah stok buku
        $stmt = $conn->prepare("UPDATE books SET stock = stock + 1 WHERE id = ?");
        $stmt->execute([$loan['book_id']]);

        $conn->commit();

        logActivity('return_book', "Pengembalian buku, denda: Rp " . number_format($fine));
        return [
            'success' => true,
            'message' => 'Pengembalian berhasil dicatat' . ($fine > 0 ? " dengan denda Rp " . number_format($fine) : ''),
            'fine' => $fine
        ];

    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Error returning book: " . $e->getMessage());
        return ['success' => false, 'errors' => ['Terjadi kesalahan sistem']];
    }
}
?>