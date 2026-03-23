<?php
$debug = false;
if($debug){
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
}
//Variables that will see the html.
$message = '';
$url = '';

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
	mkdir($uploadDir, 0755, true);
}


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
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
if (!empty($_FILES['files'])) {
	$tmp = $_FILES['files']['tmp_name'][0];
	$hash = md5_file($tmp);
	$origfilename = basename($_FILES['files']['name'][0]);
	$size = $_FILES['files']['size'][0];
	$filename = uniqid() . '_' . $origfilename;
	$target = $uploadDir . $filename;	
	//Check DB for Hash.
	
	$pdo = new PDO($dsn, $user, $pass, $options);
	$stmt = $pdo->prepare("SELECT COUNT(*) FROM files WHERE hash = :hash");
	$stmt->execute(['hash' => $hash]);
	$exists = $stmt->fetchColumn();
	if ($exists) {
    		$message = 'This file has already been uploaded.';
	}
	
	if ($debug) {
		echo "<pre>";
		var_dump('$tmp: ', $tmp);
		var_dump('$_FILES: ', $_FILES);
		var_dump('$hash: ', $hash);
		var_dump('$filename: ', $filename);
		var_dump('$target: ', $target);
		echo "</pre>";
        }

	if (move_uploaded_file($tmp, $target) && !$exists) {
		$message = "Upload successful!";
		$url = 'uploads/' . rawurlencode($filename);
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
		if ($exists) { 
		$message = "This file has already been uploaded.";
		} else {
		$message = "Failed to move uploaded file: $filename<br>";
		}
		//var_dump($tmp, $target, is_uploaded_file($tmp), file_exists($target));
	}
	} else {
		echo "No file uploaded.";
	}
}
?>
<!DOCTYPE html>
<html>
	<link rel="stylesheet" href="css/style.css">
	<head>
		<title>filehost</title>
	</head>
	<body>
		<h1>filehost</h1>
			<p>The lightweight & secure file hosting web service</p>
			<form enctype="multipart/form-data" method="POST">
				<input type="file" id="upload-input" name="files[]" multiple>
				<input type="submit" value="Submit">
			</form>
			<?php if ($message): ?>
    			<p><?= htmlspecialchars($message) ?></p>
			<a href="<?= htmlspecialchars($url) ?>" target="_blank"><?= htmlspecialchars($url) ?></a>
			<?php endif; ?>
			
		<footer>
			<p><sub>
			<a href="www.github.com/twilldigg/filehost">Source Code</a>
			 Project loosely based on my experience of the project 
			<a href="www.github.com/nokonoko/Uguu">nokonoko/Uguu<a>
			</sub></p>
		</footer>
	</body>
</html>
