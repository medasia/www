<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Form_validation extends CI_Form_validation {
	function __construct($rules = array()) {
		parent::__construct($rules);
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Date (ISO format) YYYY-MM-DD
	 *
	 * @access    public
	 * @param    string
	 * @return    bool
	 */
	function valid_date($str) {
		$pattern = '/([1-3][0-9]{3,3})-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/i';
		return (preg_match($pattern, $str) === 1) ? TRUE:FALSE;
	}
}
?>  