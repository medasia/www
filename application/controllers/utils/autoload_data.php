<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Autocomplete extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$models = array(
					'records_model' => '',
					'autocomplete_model' => ''
					);
		$this->load->model($models,'',TRUE);
	}

	function from($table)
	{
		$session_data = $this->session_userdata('logged_in');
		$data['response'] = 'false';
		switch($table)
		{
			case 'insurance':
				$result = $this->autocomplete_model->getAllRecords($table,'Name');
				break;

			case 'company':
				$result = $this->autocomplete_model->getAllRecords($table,'name');
				break;
			
			default:
				# code...
				break;
		}

		if($result > 0)
		{
			$data['response'] = 'true';
			$data[$table] = $result;
		}

		switch ($table)
		{
			case 'company':
			case 'insurance':
				if( ! $result)
				{
					return false;
				}
				$this->load->view('records/'.$table.'/'.$table.'_results_view', $data);
			break;
			
			default:
				# code...
				break;
		}
	}
}
?>