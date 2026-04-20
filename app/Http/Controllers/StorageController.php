<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller untuk serve static files dari storage
 * Digunakan ketika symlink tidak berfungsi (e.g., Windows dengan permission issues)
 */
class StorageController extends Controller
{
    /**
     * Serve cover image dari storage
     */
    public function serveCover($filename)
    {
        $path = "covers/{$filename}";
        
        // Validate filename (prevent directory traversal)
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false) {
            abort(403, 'Invalid filename');
        }
        
        // Check if file exists
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Cover not found');
        }
        
        // Get file content
        $file = Storage::disk('public')->get($path);
        
        // Get mime type
        $mimeType = Storage::disk('public')->mimeType($path);
        
        return response($file, 200, [
            'Content-Type' => $mimeType ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'public, max-age=86400', // Cache 1 hari
        ]);
    }
    
    /**
     * Generate URL untuk cover (bisa symlink atau direct serve)
     */
    public static function getCoverUrl($filename)
    {
        // Prioritas 1: Cek apakah symlink berfungsi dengan coba akses URL
        $symlinkUrl = asset('storage/covers/' . $filename);
        
        // Prioritas 2: Gunakan controller endpoint ini
        $fallbackUrl = route('storage.cover', ['filename' => $filename]);
        
        return $symlinkUrl;
    }
}
