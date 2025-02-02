<?php
/**
 * LocalServer Web Manager - [Open-source web interface for local server management]
 * 
 * @package     LocalServer-WebManager
 * @description Real-time Folders Fetching Service
 * @version     1.0.0
 * @author      Dheeraz
 * @license     MIT
 * @link        https://github.com/dheeraz101/lwm
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
require_once __DIR__ . '/log.php';

// Set base directory to htdocs (adjust this path as needed)
$BASE_DIR = realpath($_SERVER['DOCUMENT_ROOT']);

// Move functions OUTSIDE the try block
function getFolderDetails($dir) {
    global $BASE_DIR;
    $items = [];
    try {
        $iterator = new DirectoryIterator($dir);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot()) continue;
            
            $item = [
                'name' => $fileInfo->getFilename(),
                'path' => substr($fileInfo->getRealPath(), strlen($BASE_DIR) + 1),
                'size' => $fileInfo->isDir() ? folderSize($fileInfo->getRealPath()) : $fileInfo->getSize(),
                'lastModified' => date("Y-m-d H:i:s", $fileInfo->getMTime()),
                'type' => $fileInfo->isDir() ? 'directory' : 'file',
                'extension' => $fileInfo->isFile() ? $fileInfo->getExtension() : ''
            ];
            
            $items[] = $item;
        }
    } catch (UnexpectedValueException $e) {
        writeLog("Directory unreadable: $dir - " . $e->getMessage(), 'ERROR');
    }
    return $items;
}

function folderSize($dir) {
    $size = 0;
    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if ($file->isFile()) $size += $file->getSize();
        }
    } catch (UnexpectedValueException $e) {
        writeLog("Size calculation failed: $dir - " . $e->getMessage(), 'ERROR');
    }
    return $size;
}

try {
    $requestedPath = $_GET['path'] ?? './';
    $validPath = sanitizePath($requestedPath);

    $items = getFolderDetails($validPath);
    
    // Add parent directory if not at root
    if ($validPath !== $BASE_DIR) {
        array_unshift($items, [
            'name' => '..',
            'path' => dirname(substr($validPath, strlen($BASE_DIR) + 1)) ?: './',
            'type' => 'directory',
            'size' => 0,
            'lastModified' => ''
        ]);
    }

    echo json_encode([
        'success' => true,
        'data' => $items,
        'currentPath' => substr($validPath, strlen($BASE_DIR) + 1)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'data' => []
    ]);
}

// Sanitize path function remains here
function sanitizePath($inputPath) {
    global $BASE_DIR;
    
    // Handle empty path case
    $cleanPath = ltrim(urldecode($inputPath), './');
    $requestedPath = realpath($BASE_DIR . '/' . $cleanPath);

    // Add debug logging
    error_log("Sanitizing path. Input: $inputPath | Clean: $cleanPath | Result: " . ($requestedPath ?: 'NULL'));

    if (!$requestedPath || strpos($requestedPath, $BASE_DIR) !== 0) {
        writeLog("Invalid path attempt: $inputPath", 'WARNING');
        http_response_code(400);
        die(json_encode([
            'success' => false,
            'error' => 'Invalid directory path',
            'data' => []
        ]));
    }
    return $requestedPath;
}
?>