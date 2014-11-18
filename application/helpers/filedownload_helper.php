<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('file_download')) {
	function file_download($filename, $mime, $file) {

		ob_end_clean(); // cleans the output of the downloaded file

		header("Content-Length: " . filesize($file));
		header('Content-Type: '.$mime);
		header('Content-Disposition: attachment; filename='.$filename);

		readfile($file);
	}
}