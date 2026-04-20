<?php

/**
 * Helper Functions untuk Pagination
 * File ini bisa di-include di berbagai halaman untuk mendapatkan
 * informasi pagination dengan mudah
 * 
 * Usage:
 * $paginationInfo = getPaginationInfo($currentPage, $itemsPerPage, $totalItems);
 */

/**
 * Menghitung informasi pagination
 * 
 * @param int $currentPage Halaman saat ini
 * @param int $itemsPerPage Jumlah items per halaman
 * @param int $totalItems Total items
 * 
 * @return array Array berisi informasi pagination
 */
function getPaginationInfo($currentPage, $itemsPerPage, $totalItems)
{
    $currentPage = max(1, (int)$currentPage);
    $itemsPerPage = max(1, (int)$itemsPerPage);
    $totalItems = max(0, (int)$totalItems);
    
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = min($currentPage, $totalPages ?: 1);
    
    $offset = ($currentPage - 1) * $itemsPerPage;
    $firstItem = $totalItems > 0 ? $offset + 1 : 0;
    $lastItem = min($offset + $itemsPerPage, $totalItems);
    
    return [
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'totalItems' => $totalItems,
        'itemsPerPage' => $itemsPerPage,
        'offset' => $offset,
        'firstItem' => $firstItem,
        'lastItem' => $lastItem,
        'hasPrevious' => $currentPage > 1,
        'hasNext' => $currentPage < $totalPages,
        'previousPage' => max(1, $currentPage - 1),
        'nextPage' => min($totalPages, $currentPage + 1),
    ];
}

/**
 * Generate HTML pagination links
 * 
 * @param int $currentPage Halaman saat ini
 * @param int $totalPages Total halaman
 * @param string $baseUrl URL dasar (dengan ?page= atau &page=)
 * @param int $surroundingPages Jumlah halaman di sekitar current page
 * 
 * @return string HTML pagination links
 */
function generatePaginationHTML($currentPage, $totalPages, $baseUrl, $surroundingPages = 2)
{
    if ($totalPages <= 1) {
        return '';
    }
    
    $html = '<nav aria-label="Pagination" class="my-4">' . "\n";
    $html .= '<ul class="pagination mb-0">' . "\n";
    
    // First & Previous buttons
    if ($currentPage > 1) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . htmlspecialchars($baseUrl) . 'page=1">« First</a>';
        $html .= '</li>' . "\n";
        
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . htmlspecialchars($baseUrl) . 'page=' . ($currentPage - 1) . '">‹ Prev</a>';
        $html .= '</li>' . "\n";
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">« First</span></li>' . "\n";
        $html .= '<li class="page-item disabled"><span class="page-link">‹ Prev</span></li>' . "\n";
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - $surroundingPages);
    $endPage = min($totalPages, $currentPage + $surroundingPages);
    
    for ($page = $startPage; $page <= $endPage; $page++) {
        if ($page === $currentPage) {
            $html .= '<li class="page-item active">';
            $html .= '<span class="page-link">' . $page . ' <span class="visually-hidden">(current)</span></span>';
            $html .= '</li>' . "\n";
        } else {
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="' . htmlspecialchars($baseUrl) . 'page=' . $page . '">' . $page . '</a>';
            $html .= '</li>' . "\n";
        }
    }
    
    // Next & Last buttons
    if ($currentPage < $totalPages) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . htmlspecialchars($baseUrl) . 'page=' . ($currentPage + 1) . '">Next ›</a>';
        $html .= '</li>' . "\n";
        
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . htmlspecialchars($baseUrl) . 'page=' . $totalPages . '">Last »</a>';
        $html .= '</li>' . "\n";
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Next ›</span></li>' . "\n";
        $html .= '<li class="page-item disabled"><span class="page-link">Last »</span></li>' . "\n";
    }
    
    $html .= '</ul>' . "\n";
    $html .= '</nav>' . "\n";
    
    return $html;
}

/**
 * Generate pagination info text
 * 
 * @param int $firstItem Item pertama
 * @param int $lastItem Item terakhir
 * @param int $totalItems Total items
 * @param int $currentPage Halaman saat ini
 * @param int $totalPages Total halaman
 * 
 * @return string Info text
 */
function getPaginationText($firstItem, $lastItem, $totalItems, $currentPage, $totalPages)
{
    if ($totalItems === 0) {
        return 'Tidak ada data';
    }
    
    return sprintf(
        'Menampilkan <strong>%d-%d</strong> dari <strong>%d</strong> data | Halaman <strong>%d</strong> dari <strong>%d</strong>',
        $firstItem,
        $lastItem,
        $totalItems,
        $currentPage,
        $totalPages
    );
}

/**
 * Get LIMIT clause untuk database query
 * 
 * @param int $currentPage Halaman saat ini
 * @param int $itemsPerPage Items per page
 * 
 * @return string LIMIT clause (e.g., "LIMIT 10 OFFSET 0")
 */
function getLimitClause($currentPage, $itemsPerPage)
{
    $currentPage = max(1, (int)$currentPage);
    $itemsPerPage = max(1, (int)$itemsPerPage);
    
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    return "LIMIT {$itemsPerPage} OFFSET {$offset}";
}

/**
 * Get LIMIT & OFFSET untuk PDO atau MySQLi prepared statement
 * 
 * @param int $currentPage Halaman saat ini
 * @param int $itemsPerPage Items per page
 * 
 * @return array ['limit' => ..., 'offset' => ...]
 */
function getLimitOffset($currentPage, $itemsPerPage)
{
    $currentPage = max(1, (int)$currentPage);
    $itemsPerPage = max(1, (int)$itemsPerPage);
    
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    return [
        'limit' => $itemsPerPage,
        'offset' => $offset,
    ];
}

/**
 * Generate complete pagination HTML dengan info text
 * 
 * @param int $currentPage Halaman saat ini
 * @param int $totalPages Total halaman
 * @param int $totalItems Total items
 * @param string $baseUrl Base URL untuk pagination
 * @param int $surroundingPages Halaman di sekitar current
 * 
 * @return string Complete HTML
 */
function generateCompletePagination($currentPage, $totalPages, $totalItems, $baseUrl, $surroundingPages = 2)
{
    $paginationInfo = getPaginationInfo($currentPage, 10, $totalItems);
    
    $html = '<div class="d-flex justify-content-between align-items-center my-4">' . "\n";
    
    // Info text
    $html .= '<div class="text-muted small">' . "\n";
    $html .= getPaginationText(
        $paginationInfo['firstItem'],
        $paginationInfo['lastItem'],
        $paginationInfo['totalItems'],
        $currentPage,
        $totalPages
    ) . "\n";
    $html .= '</div>' . "\n";
    
    // Pagination links
    $html .= generatePaginationHTML($currentPage, $totalPages, $baseUrl, $surroundingPages);
    
    $html .= '</div>' . "\n";
    
    return $html;
}
