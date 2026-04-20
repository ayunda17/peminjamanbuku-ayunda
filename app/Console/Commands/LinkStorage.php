<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LinkStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:link-fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create storage symlink dan fix permissions untuk cover images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        // Cek apakah target folder ada
        if (!is_dir($target)) {
            $this->error("Target folder tidak ada: $target");
            return Command::FAILURE;
        }

        // Cek apakah symlink sudah ada
        if (is_link($link)) {
            $this->info("✅ Symlink sudah ada: $link -> $target");
        } elseif (is_dir($link)) {
            $this->warn("⚠️  Folder $link sudah ada (bukan symlink). Mencoba menghapus...");
            try {
                // Coba hapus folder existing
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($link, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                );
                
                foreach ($files as $fileinfo) {
                    $fileinfo->isDir() ? rmdir($fileinfo->getRealPath()) : unlink($fileinfo->getRealPath());
                }
                rmdir($link);
                $this->info("Folder lama dihapus");
            } catch (\Exception $e) {
                $this->error("Gagal menghapus folder: " . $e->getMessage());
                return Command::FAILURE;
            }
        }

        // Buat symlink
        try {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows
                $output = shell_exec("mklink /D \"$link\" \"$target\" 2>&1");
                if (strpos($output, 'created') !== false || is_link($link)) {
                    $this->info("✅ Symlink berhasil dibuat di Windows");
                } else {
                    $this->error("❌ Gagal membuat symlink: $output");
                    return Command::FAILURE;
                }
            } else {
                // Unix/Linux/Mac
                if (!symlink($target, $link)) {
                    $this->error("❌ Gagal membuat symlink");
                    return Command::FAILURE;
                }
                $this->info("✅ Symlink berhasil dibuat");
            }

            // Verify symlink
            if (is_link($link)) {
                $this->info("✅ Symlink berhasil diverifikasi");
                
                // Check permissions
                $this->info("\n📁 Struktur folder:");
                $this->info("  Storage: $target");
                $this->info("  Symlink: $link");
                $this->info("  Covers: $target/covers");
                
                if (is_dir("$target/covers")) {
                    $coverCount = count(glob("$target/covers/*"));
                    $this->info("  📸 Total cover images: $coverCount");
                }
                
                return Command::SUCCESS;
            } else {
                $this->error("❌ Symlink tidak valid setelah dibuat");
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
