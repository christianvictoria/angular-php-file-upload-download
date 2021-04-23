<?php
	if (isset($_GET['filename'])) {
		$filename = $_GET['filename'];
		$file_path = "uploads/$filename";
		if (file_exists($file_path)) {
			header("Access-Control-Allow-Origin: *");
			header("Content-Type: application/json; charset=utf-8; application/octet-stream");
			header("Access-Control-Allow-Methods: POST");
			header('Content-Description: File Transfer');
		    header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file_path));
		    readfile($file_path);
		    exit;
	    }
	}
?>