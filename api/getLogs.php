<?php
/**
 * LocalServer Web Manager - [Open-source web interface for local server management]
 * 
 * @package     LocalServer-WebManager
 * @description Get Logs for all the events that have occurred
 * @version     1.0.0
 * @author      Dheeraz
 * @license     MIT
 * @link        https://github.com/dheeraz101/lwm
 */
header('Content-Type: text/plain');
header('Access-Control-Allow-Origin: *');
require_once 'log.php';

$logFile = realpath(__DIR__ . '/../logs/system.log'); 

try {
    if (!file_exists($logFile)) {
        throw new Exception('No log file found');
    }
    
    if (filesize($logFile) > 5 * 1024 * 1024) { // 5MB
        throw new Exception('Log file too large to display');
    }

    $logs = file_get_contents($logFile);
    if ($logs === false) {
        throw new Exception('Failed to read log file');
    }

    echo $logs;
    writeLog("Logs viewed successfully", 'INFO');

} catch (Exception $e) {
    http_response_code(500);
    echo "Error displaying logs: " . $e->getMessage();
    writeLog("Log view error: " . $e->getMessage(), 'ERROR');
}
?>