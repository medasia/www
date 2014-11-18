<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Hospaccnt extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('records_model','',TRUE);
		$this->load->helper('url');
		$this->load->library('pagination');

		if($this->session->userdata('logged_in'))
		{
			//set header links depending on logged in users in userdata session
			$this->header_links = $this->session->userdata('logged_in');

			$session_data = $this->session->userdata('logged_in');

			switch($session_data['usertype'])
			{
				case 'sysad':
				case 'accounting':
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
				case 'accounting':
						$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/hospaccnt/hospaccnt_search_view' => NULL
							// 'records/hospaccnt/hospaccnt_register_view' => NULL,
							// 'records/hospaccnt/hospaccnt_view' => NULL
							);
						$this->load->template($loadedViews, $this->header_links);
				break;

				default:
					echo '<script>alert("You are not allowed to access this portion of the site!");</script>';
					redirect('','refresh');
			}
		}
	}

	function search()
	{
		$this->session->unset_userdata('sess_hospaccnt');
		$this->form_validation->set_rules('keyword','Keyword','trim|xss_clean');
		$this->form_validation->set_rules('limit', 'Limit','trim|xss_clena');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('records/hospaccnt','refresh');
		}
		else
		{
			if($_POST['submit'] == 'Search Hospital')
			{
				$table = 'hospital';
			}
			if($_POST['submit'] == 'Search Doctors')
			{
				$table = 'dentistsanddoctors';
			}
			$keyword = $_POST['keyword'];
			$limit = $_POST['limit'];

			$sess_hospaccnt = array(
							'table' => $table,
							'keyword' => $keyword,
							'limit' => $limit
							);
			$this->session->set_userdata('sess_hospaccnt',$sess_hospaccnt);

			if($limit < '500000')
			{
				$config['base_url'] = base_url().'records/hospaccnt/sessionHospaccnt';
				$config['total_rows'] = $this->records_model->countHospaccnt($table,$keyword,$limit);
				$config['per_page'] = $limit;
				$config['uri_segment'] = 4;
				$choice = $config['total_rows'] / $config['per_page'];
				$config['num_links'] = 10;
				$config['prev_link'] = '<< Previous';
				$config['next_link'] = 'Next >>';
				$this->pagination->initialize($config);

				$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
				$data['links'] = $this->pagination->create_links();
			}
			else
			{
				$config = NULL;
				$page = 0;
			}

			$result = $this->records_model->searchByLikes($table,$keyword,$limit,$page);
			$data['result'] = $result;

			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/hospaccnt/hospaccnt_search_view' => NULL,
							'records/hospaccnt/hospaccnt_result_view' => $data
							);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function sessionHospaccnt()
	{
		if($this->session->userdata('sess_hospaccnt'))
		{	
			// var_dump($this->session->userdata('sess_hospaccnt'));
			$sess_hospaccnt = $this->session->userdata('sess_hospaccnt');

			$table = $sess_hospaccnt['table'];
			$keyword = $sess_hospaccnt['keyword'];
			$limit = $sess_hospaccnt['limit'];

			$config['base_url'] = base_url().'records/hospaccnt/sessionHospaccnt';
			$config['total_rows'] = $this->records_model->countHospaccnt($table,$keyword,$limit);
			$config['per_page'] = $limit;
			$config['uri_segment'] = 4;
			$choice = $config['total_rows'] / $config['per_page'];
			$config['num_links'] = 10;
			$config['prev_link'] = '<< Previous';
			$config['next_link'] = 'Next >>';
			$this->pagination->initialize($config);

			$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
			$data['links'] = $this->pagination->create_links();

			$result = $this->records_model->searchByLikes($table,$keyword,$limit,$page);
			$data['result'] = $result;

			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/hospaccnt/hospaccnt_search_view' => NULL,
							'records/hospaccnt/hospaccnt_result_view' => $data
							);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function view($table,$id)
	{
		$result = $this->records_model->getRecordById($table, $id);
		if($result)
		{
			foreach($result as $row)
			{
				if($table == 'hospital')
				{
					$loadedViews = array(
									'records/records_header_view' => $this->header_links,
									'records/hospaccnt/hospaccnt_view_hospital_view' => $row
									);
					$this->load->template($loadedViews,$this->header_links);
				}
				if($table == 'dentistsanddoctors')
				{
					$loadedViews = array(
									'records/records_header_view' => $this->header_links,
									'records/hospaccnt/hospaccnt_view_dentistsanddoctors_view' => $row
									);
					$this->load->template($loadedViews,$this->header_links);
				}
			}
		}
	}

	function register() {
		//Validates inputs from user, checks for security flaws
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('vendor_account', 'vendor_account', 'trim|required|xss_clean');
		$this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('terms', 'Terms', 'trim|required|xss_clean');
		$this->form_validation->set_rules('vat', 'Vat', 'trim|required|xss_clean');
		$this->form_validation->set_rules('days', 'Days', 'trim|required|xss_clean');
		$this->form_validation->set_rules('clinic_hospital', 'Clinic/Hospital', 'trim|xss_clean');
		
		if($this->form_validation->run() == FALSE) { //if validation had errors reroute to useraccounts with flashdata that contains the said errors
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/hospaccnt', 'refresh');
		} else {
			$data = $_POST;
			unset($data['submit']);
			$register = $this->records_model->register('hospital_account', $data);

			if($register) {
				$this->session->set_flashdata('result', 'Succesfully registered Hospital Account.');
				redirect('records/hospaccnt', 'refresh');
			} else {
				$this->form_validation->set_message('register', 'Something is wrong');
				return false;
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
			redirect('records/hospaccnt', 'refresh');
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
		$data = $this->session->flashdata('data');

		if($data['submit'] == 'Delete')
		{
			$count = 0;
			foreach($data['selMulti'] as $id)
			{
				$delete = $this->records_model->delete('hospital_account', $id);
				$count++;
			}
			if($delete)
			{ //if successfully deleted company-insurance, reroute to compins with flashdata
				$this->session->set_flashdata('result', 'Deleted '.$count.' records of Hospital Account.');
				redirect('records/hospaccnt', 'refresh');
			}
		}
	}
	
	function delete($id) {
		$delete = $this->records_model->delete('hospital_account', $id);
		if($delete) { //if successfully deleted hospical account, reroute to hospaccnt with flashdata
			$this->session->set_flashdata('result', 'Deleted account.');
			redirect('records/hospaccnt', 'refresh');
		}
	}
}
?>