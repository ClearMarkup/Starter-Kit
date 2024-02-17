<?php
// start.php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Append the path to the public directory
$filePath = __DIR__ . '/../public' . $path;

// If the file exists in the public directory, read and output its contents.
if (file_exists($filePath) && !is_dir($filePath)) {
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($fileInfo, $filePath);
    finfo_close($fileInfo);

    // Check if the file is a CSS file
    if (pathinfo($filePath, PATHINFO_EXTENSION) === 'css') {
        $mimeType = 'text/css';
    } else if (pathinfo($filePath, PATHINFO_EXTENSION) === 'js') {
        $mimeType = 'text/javascript';
    } else if (pathinfo($filePath, PATHINFO_EXTENSION) === 'svg') {
        $mimeType = 'image/svg+xml';
    }

    header("Content-Type: $mimeType");
    readfile($filePath);
    exit;
}

include_once __DIR__ . '/../index.php';