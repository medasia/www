<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Company extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('records_model','',TRUE);
		if($this->session->userdata('logged_in'))
		{
			//set header links depending on logged in users in userdata session
			$this->header_links = $this->session->userdata('logged_in');

			$session_data = $this->session->userdata('logged_in');
			switch($session_data['usertype'])
			{
				case 'sysad':
				case 'admin_assoc':
				case 'claims':
				break;

				default:
					echo '<script>alert("You are not allowed to access this portion of the site!");</script>';
					redirect('','refresh');
			}
		}
		else
		{
			//If no session, redirect to login page
			redirect('../', 'refresh');
		}
	}

	function index()
	{
		if($this->session->userdata('logged_in'))
		{
			$session_data = $this->session->userdata('logged_in');

			switch($session_data['usertype'])
			{
				case 'sysad':
				case 'admin_assoc':
				case 'claims':
					$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/company/company_register_view' => NULL,
							'records/company/company_view' => NULL
							);
					$this->load->template($loadedViews, $this->header_links);
				break;

				default:
					echo '<script>alert("You are not allowed to access this portion of the site!");</script>';
					redirect('','refresh');
			}
		}
		
	}

	function view($companyId)
	{
		$result = $this->records_model->getRecordById('company', $companyId);
		if($result)
		{
			foreach($result as $row)
			{
				$loadedViews = array(
									// 'records/records_header_view' => $this->header_links,
									'records/company/company_view_company_view' => $row
									);
				$this->load->template($loadedViews);
			}
		}
		else
		{
			$this->session->set_flashdata('result', '<b>Record not found, may be deleted or an error occured.</b>');
			redirect('records/company', 'refresh');
		}
	}
	
	function register()
	{
		//Validates inputs from user, checks for security flaws
		$this->form_validation->set_rules('name', 'Company', 'trim|required|xss_clean');
		$this->form_validation->set_rules('code', 'Code', 'trim|required|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{ //if validation had errors reroute to useraccounts with flashdata that contains the said errors
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/company', 'refresh');
		}
		else
		{
			$data = $_POST;
			unset($data['submit']);
			$register = $this->records_model->register('company', $data);

			if($register)
			{
				$this->session->set_flashdata('result', '<b>Succesfully registered Company.</b>');
				redirect('records/accounts', 'refresh');
			}
			else
			{
				$this->session->set_flashdata('result', '<b>Error in registering data, database error or duplicate data may occured.');
				redirect('records/accounts','refresh');
			}
		}
	}

	function multiSelect()
	{
		// var_dump($_POST);
		$this->form_validation->set_rules('selMulti[]', 'Multiple Select', 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			echo "INPUT ERROR: All fields are required!!";
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/company', 'refresh');
		}
		else
		{
			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/verify_password_view' => NULL
							);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function multiVerified()
	{
		$data =  $this->session->flashdata('data');
		
		if($data['submit'] == 'Delete')
		{
			$count = 0;
			foreach($data['selMulti'] as $id)
			{
				$delete = $this->records_model->delete('company', $id);
				$count++;
			}
			if($delete)
			{ //if successfully deleted company-insurance, reroute to compins with flashdata
				$this->session->set_flashdata('result', 'Deleted '.$count.' records of Company.');
				redirect('records/accounts', 'refresh');
			}
		}
	}
	
	function delete($id) {
		$delete = $this->records_model->delete('company', $id);
		if($delete) { //if successfully deleted company, reroute to Company with flashdata
			$this->session->set_flashdata('result', 'Deleted company.');
			redirect('records/accounts', 'refresh');
		}
	}

	function search()
	{
		$this->form_validation->set_rules('company', 'Company', 'trim|xss_clean');
		$this->form_validation->set_rules('limit', 'Limit', 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/company', 'refresh');
		}
		else
		{
			unset($_POST['submit']);
			$keyword = $_POST['companyM'];
			$limit = $_POST['limit'];

			$result = $this->records_model->getRecord('company', $keyword, $limit);
			
			foreach($result as $key => $value)
			{
				$data['message'][] = array('label'=>$value['name'], 'value'=>$value['name']);
			}
			$data['company'] = $result;

			$loadedViews = array(
						'records/records_header_view' => $this->header_links,
						'records/company/company_register_view' => NULL,
						'records/company/company_view' => NULL,
						'records/company/company_results_view' => $data
						);
			$this->load->template($loadedViews, $this->header_links);
		}
	}
}
?>