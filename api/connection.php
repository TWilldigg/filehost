<?php
$debug = true;
if($debug){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

require __DIR__ . '/load_env.php';

$env = loadEnv(__DIR__ . '/../.env');
$socket  = $env['DB_SOCKET'];
$db      = $env['DB_NAME'];
$user    = $env['DB_USER'];
$pass    = $env['DB_PASS'];
$charset = 'utf8mb4';

$dsn = "mysql:unix_socket=$socket;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "✅ Connection via socket successful!";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
