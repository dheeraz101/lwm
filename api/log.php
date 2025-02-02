<?php
/**
 * LocalServer Web Manager - [Open-source web interface for local server management]
 * 
 * @package     LocalServer-WebManager
 * @description Generate a logs file for the system.
 * @version     1.0.0
 * @author      Dheeraz
 * @license     MIT
 * @link        https://github.com/dheeraz101/lwm
 */
function writeLog($message, $type = 'INFO') {
    $logDir = realpath(__DIR__ . '/../logs');
    $logFile = $logDir . '/system.log';
    
    // Create directory if missing
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }

    // Validate message
    $cleanMessage = trim(preg_replace('/\s+/', ' ', $message));
    $entry = sprintf(
        "[%s][%s][%s] %s\n",
        date('Y-m-d H:i:s'),
        $type,
        $_SERVER['REMOTE_ADDR'] ?? 'CLI',
        $cleanMessage
    );
    
    // Write to log
    file_put_contents($logFile, $entry, FILE_APPEND);
}
?>