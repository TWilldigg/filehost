<?php
// Needs complete rework
$debug = false;
$uploadDir = __DIR__ . '/uploads/';
$hashLogFile = __DIR__ . '/hashes';

if (!is_dir($uploadDir)) {
	mkdir($uploadDir, 0755, true);
};

if (!empty($_FILES['files'])) {
	$tmp = $_FILES['files']['tmp_name'][0];
	$hash = md5_file($tmp);
	$filename = uniqid() . '_' . basename($_FILES['files']['name'][0]);
	$target = $uploadDir . $filename;	
	$duplicate = false;
	if (file_exists($hashLogFile)) {
		$fh = fopen($hashLogFile, 'r');
		while (($line = fgets($fh)) !== false) {
			$line = trim($line);
			if($line === '') continue;
			$lineArray = explode(' ', $line, 2);
			$loggedHash = $lineArray[0];
			$loggedFilename = $lineArray[1];
			if ($hash === $loggedHash) {
				$duplicate = true;
				$dupeFilename = $loggedFilename;
				break;
			}
		}
	fclose($fh);
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
		file_put_contents($hashLogFile, $hash . ' ' . $filename . PHP_EOL, FILE_APPEND | LOCK_EX);
	} else {
		echo "Failed to move uploaded file: $filename<br>";
		var_dump($tmp, $target, is_uploaded_file($tmp), file_exists($target));
	};
	} else {
	echo "No file uploaded.";
}
