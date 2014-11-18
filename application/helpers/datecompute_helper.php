<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('computeAge')) {
	function computeAge($dateofbirth) {
		return floor((time() - strtotime($dateofbirth))/31556926);
	}   
}
?>