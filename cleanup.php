<?php
// Cleanup script to remove old files
// Run this script periodically via cron job

$directories = ['uploads', 'processed', 'downloads'];
$max_age = 3600; // 1 hour in seconds

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file) && (time() - filemtime($file)) > $max_age) {
                unlink($file);
                echo "Deleted: $file\n";
            }
        }
        
        // Also remove empty subdirectories
        $subdirs = glob($dir . '/*', GLOB_ONLYDIR);
        foreach ($subdirs as $subdir) {
            if (count(scandir($subdir)) == 2) { // Only . and .. entries
                rmdir($subdir);
                echo "Removed empty directory: $subdir\n";
            }
        }
    }
}

echo "Cleanup completed at " . date('Y-m-d H:i:s') . "\n";
?>