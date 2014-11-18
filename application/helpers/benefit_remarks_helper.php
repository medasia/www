<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('benefit_remarks'))
{
	function benefit_remarks($benefit_name)
	{
		$CI =& get_instance();
		$CI->load->model('benefit_model');

		$remarks = $CI->benefit_model->getRecordByField('benefits.benefits_remarks','benefit_name',$benefit_name);

		foreach($remarks as $key => $value)
		{
			return $value['remarks'];
		}
	}
}