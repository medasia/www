<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('benefit_details'))
{
	function benefit_details($benefit_name)
	{
		$CI =& get_instance();
		$CI->load->model('benefit_model');

		$details = $CI->benefit_model->getBenefitsDetails($benefit_name);
		return $details;
	}
}