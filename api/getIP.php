<?php
/**
 * LocalServer Web Manager - [Open-source web interface for local server management]
 * 
 * @package     LocalServer-WebManager
 * @description Get Local Ip Address
 * @version     1.0.0
 * @author      Dheeraz
 * @license     MIT
 * @link        https://github.com/dheeraz101/lwm
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Cross-platform IP detection
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $ip = gethostbyname(trim(`hostname`));
    } else {
        $ip = shell_exec('hostname -I');
        $ips = explode(" ", trim($ip));
        $ip = $ips[0] ?? $_SERVER['SERVER_ADDR'];
    }
    
    echo json_encode(['ip' => filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1']);
} catch (Exception $e) {
    echo json_encode(['ip' => 'Unavailable']);
}
?>