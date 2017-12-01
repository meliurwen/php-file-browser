<?php
	
	$directory = "/home/user/";
	$file = "cacca.mp3";
	$filePath = $directory . $file;

	// Set up the download system...
	header('Content-Description: File Transfer');
	header('Content-Type: '. mime_content_type($filePath));
	header('Content-Disposition: attachment; filename=' . $file);
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: '.filesize($filePath));

	// Flush the cache
	ob_clean();
	flush();

	// Send file to browser
	readfile($filePath);

	// DO NOT DO ANYTHING AFTER FILE DOWNLOAD
	exit;



?>
