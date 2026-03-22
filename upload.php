<?php
// Needs complete rework
$debug = false;
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
	mkdir($uploadDir, 0755, true);
};


//Establish DB Connection Info
require __DIR__ . '/api/load_env.php';

$env = loadEnv(__DIR__ . '/.env');
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

//CHECK if FILES is empty
//generate and insert new DB Field.

if (!empty($_FILES['files'])) {
	$tmp = $_FILES['files']['tmp_name'][0];
	$hash = md5_file($tmp);
	$origfilename = basename($_FILES['files']['name'][0]);
	$size = $_FILES['files']['size'][0];
	$filename = uniqid() . '_' . $origfilename;
	$target = $uploadDir . $filename;	
	$duplicate = false;
	//Check DB for Hash.
	
	$pdo = new PDO($dsn, $user, $pass, $options);
	$stmt = $pdo->prepare("SELECT COUNT(*) FROM files WHERE hash = :hash");
	$stmt->execute(['hash' => $hash]);
	$exists = $stmt->fetchColumn();
	if ($exists) {
    		die("This file has already been uploaded.");
	}
	
	if ($debug) {
		echo "<pre>";
		var_dump('$tmp: ', $tmp);
		var_dump('$_FILES: ', $_FILES);
		var_dump('$duplicate: ', $duplicate);
		var_dump('$hash: ', $hash);
		var_dump('$filename: ', $filename);
		var_dump('$target: ', $target);
		var_dump('$dupeFilename: ', $dupeFilename);
		echo "</pre>";
        }

	if ($duplicate) {
		$fileUrl = 'uploads/' . basename($dupeFilename);
    		echo "Duplicate detected: $name already uploaded as <a href='$fileUrl'>$dupeFilename</a><br>";
		exit;
	}	
	if (move_uploaded_file($tmp, $target)) {
		echo "Uploaded: <a href='uploads/$filename'>$filename</a><br>";
		//Insert DB
		$stmt = $pdo->prepare("
				INSERT INTO files (hash, stored_name, original_name, size)
				VALUES (:hash, :stored_name, :original_name, :size)			
		");
		$stmt->execute([
			'hash' => $hash,
			'stored_name' => $filename,
			'original_name' => $origfilename,
			'size' => $size,
		]);
		
	} else {
		echo "Failed to move uploaded file: $filename<br>";
		var_dump($tmp, $target, is_uploaded_file($tmp), file_exists($target));
	};
	} else {
	echo "No file uploaded.";
}
