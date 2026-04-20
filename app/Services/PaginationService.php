<?php

namespace App\Services;

/**
 * Service untuk menangani paginasi sederhana
 */
class PaginationService
{
    private $page;
    private $perPage;
    private $totalItems;
    private $totalPages;
    private $offset;

    /**
     * Constructor
     */
    public function __construct($currentPage = 1, $perPage = 10)
    {
        $this->page = max(1, (int) $currentPage);
        $this->perPage = max(1, (int) $perPage);
        $this->totalItems = 0;
        $this->totalPages = 0;
        $this->offset = 0;
    }

    /**
     * Set total items dan hitung pagination
     */
    public function setTotal($totalItems)
    {
        $this->totalItems = max(0, (int) $totalItems);
        $this->totalPages = ceil($this->totalItems / $this->perPage);
        
        // Validasi halaman tidak melebihi total halaman
        if ($this->page > $this->totalPages && $this->totalPages > 0) {
            $this->page = $this->totalPages;
        }
        
        $this->calculateOffset();
        return $this;
    }

    /**
     * Hitung offset untuk query LIMIT
     */
    private function calculateOffset()
    {
        $this->offset = ($this->page - 1) * $this->perPage;
    }

    /**
     * Get halaman saat ini
     */
    public function getCurrentPage()
    {
        return $this->page;
    }

    /**
     * Get jumlah item per halaman
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Get total items
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }

    /**
     * Get total halaman
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * Get offset untuk LIMIT query
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Get nomor item pertama di halaman saat ini
     */
    public function getFirstItemNumber()
    {
        if ($this->totalItems === 0) {
            return 0;
        }
        return $this->offset + 1;
    }

    /**
     * Get nomor item terakhir di halaman saat ini
     */
    public function getLastItemNumber()
    {
        if ($this->totalItems === 0) {
            return 0;
        }
        return min($this->offset + $this->perPage, $this->totalItems);
    }

    /**
     * Check apakah ada halaman sebelumnya
     */
    public function hasPreviousPage()
    {
        return $this->page > 1;
    }

    /**
     * Check apakah ada halaman berikutnya
     */
    public function hasNextPage()
    {
        return $this->page < $this->totalPages;
    }

    /**
     * Get halaman sebelumnya
     */
    public function getPreviousPage()
    {
        return max(1, $this->page - 1);
    }

    /**
     * Get halaman berikutnya
     */
    public function getNextPage()
    {
        return min($this->totalPages, $this->page + 1);
    }

    /**
     * Get array halaman yang akan ditampilkan (dengan surrounding pages)
     * Contoh: halaman 5 dengan range 2, maka tampilkan [3, 4, 5, 6, 7]
     */
    public function getPageRange($surroundingPages = 2)
    {
        $startPage = max(1, $this->page - $surroundingPages);
        $endPage = min($this->totalPages, $this->page + $surroundingPages);

        return range($startPage, $endPage);
    }

    /**
     * Get data untuk view
     */
    public function toArray()
    {
        return [
            'currentPage' => $this->getCurrentPage(),
            'perPage' => $this->getPerPage(),
            'totalItems' => $this->getTotalItems(),
            'totalPages' => $this->getTotalPages(),
            'offset' => $this->getOffset(),
            'firstItem' => $this->getFirstItemNumber(),
            'lastItem' => $this->getLastItemNumber(),
            'hasPreviousPage' => $this->hasPreviousPage(),
            'hasNextPage' => $this->hasNextPage(),
            'previousPage' => $this->getPreviousPage(),
            'nextPage' => $this->getNextPage(),
            'pageRange' => $this->getPageRange(),
        ];
    }
}
