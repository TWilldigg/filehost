<?php
$debug = true;
$uploadDir = __DIR__ . '/uploads/';

if ($debug) {
echo '<pre>';
print_r($_FILES);
echo '</pre>';
}

if (!is_dir($uploadDir)) {
	mkdir($uploadDir, 0755, true);
};

if (!empty($_FILES['files'])) {
	$tmp = $_FILES['files']['tmp_name'][0];
	$filename = uniqid() . '_' . basename($_FILES['files']['name'][0]);
	$target = $uploadDir . $filename;	

	if ($debug) {
		echo "<pre>";
		var_dump($tmp, $filename, $target);
		var_dump(is_uploaded_file($tmp));
		var_dump(file_exists($target));
		echo "</pre>";
	}

	if (move_uploaded_file($tmp, $target)) {
		echo "Uploaded: <a href='uploads/$filename'>$filename</a><br>";
	} else {
		echo "Failed to move uploaded file: $filename<br>";
		var_dump($tmp, $target, is_uploaded_file($tmp), file_exists($target));
	};
} else {
	echo "No file uploaded.";
}
