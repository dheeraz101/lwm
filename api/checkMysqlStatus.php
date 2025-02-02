<?php
/**
 * LocalServer Web Manager - [Open-source web interface for local server management]
 * 
 * @package     LocalServer-WebManager
 * @description Real-time MySQL service status checker with connection testing
 * @version     1.0.0
 * @author      Dheeraz
 * @license     MIT
 * @link        https://github.com/dheeraz101/lwm
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
require_once __DIR__ . '/log.php';

// Load config from environment
$configFile = realpath(__DIR__ . '/../config/config.env');
if (!$configFile) {
    writeLog("Config file missing", 'ERROR');
    die(json_encode(['error' => 'Config file missing']));
}

$config = parse_ini_file($configFile);
if (!$config) {
    writeLog("Invalid config format", 'ERROR');
    die(json_encode(['error' => 'Invalid config format']));
}

try {
    $conn = new mysqli(
        $config['DB_HOST'],
        $config['DB_USER'],
        $config['DB_PASS'],
        $config['DB_NAME'],
        $config['DB_PORT']
    );

    if ($conn->connect_error) {
        writeLog("MySQL connection failed: {$conn->connect_error}", 'ERROR');
        throw new Exception('Database connection failed');
    }

    // Get server stats
    $result = $conn->query("SHOW GLOBAL STATUS LIKE 'Uptime'");
    if (!$result) {
        throw new Exception('Failed to retrieve uptime');
    }

    $uptimeRow = $result->fetch_assoc();
    if (!$uptimeRow) {
        throw new Exception('Failed to fetch uptime value');
    }

    $uptime = $uptimeRow['Value'];
    $version = $conn->server_version;

    echo json_encode([
        'status' => 'Running',
        'version' => $version,
        'uptime' => gmdate('H\h i\m s\s', $uptime),
        'timestamp' => time()
    ]);
} catch (Exception $e) {
    http_response_code(503);
    echo json_encode([
        'status' => 'Not Running',
        'error' => $e->getMessage(),
        'timestamp' => time()
    ]);
    writeLog("MySQL Error: {$e->getMessage()}", 'CRITICAL');
} finally {
    if ($conn) {
        $conn->close();
    }
}
?>