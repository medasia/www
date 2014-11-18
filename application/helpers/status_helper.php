<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('status_update'))
{
	function status_update($table,$field,$data,$key)
	{
		$CI =& get_instance();
		$CI->load->model('records_model');

		$CI->records_model->update($table,$field,$data,$key);
		$result = $CI->records_model->getField($table,$field,$key);
		return $result;
	}
}