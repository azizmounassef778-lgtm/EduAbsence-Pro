<?php

$dir = __DIR__ . '/resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$count = 0;

foreach ($files as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $lines = file($file->getPathname());
        foreach ($lines as $num => $line) {
            if (preg_match('/[\x{1F000}-\x{1FFFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $line, $matches)) {
                $count++;
                echo $file->getFilename() . " L" . ($num + 1) . ": " . trim($line) . PHP_EOL;
            }
        }
    }
}

echo "Total emojis found: $count" . PHP_EOL;
