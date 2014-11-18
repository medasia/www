<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Emerroom extends CI_Controller
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
	function index() {
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
							'records/emerroom/emerroom_register_view' => NULL,
							'records/emerroom/emerroom_view' => NULL
							);
					$this->load->template($loadedViews, $this->header_links);
				break;

				default:
					echo '<script>alert("You are not allowed to access this portion of the site!");</script>';	
					redirect('','refresh');
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
			redirect('records/emerroom', 'refresh');
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
				$delete = $this->records_model->delete('emergency_room', $id);
				$count++;
			}
			if($delete)
			{ //if successfully deleted company-insurance, reroute to compins with flashdata
				$this->session->set_flashdata('result', 'Deleted '.$count.' records of Emergency Room.');
				redirect('records/emerroom', 'refresh');
			}
		}
	}

	function register() {
		//Validates inputs from user, checks for security flaws
		$this->form_validation->set_rules('card_number', 'Card #', 'trim|required|xss_clean');
		$this->form_validation->set_rules('pin_number', 'Pin #', 'trim|required|xss_clean');
		$this->form_validation->set_rules('amount','Amount','trim|required|xss_clean');
		$this->form_validation->set_rules('dateexpiration','Date of Expiration','trim|required|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{ //if validation had errors reroute to useraccounts with flashdata that contains the said errors
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/emerroom', 'refresh');
		}
		else
		{
			$data = $_POST;
			$user = $this->session->userdata('logged_in');
			if($user['usertype'] == 'admin_assoc' || $user['usertype'] == 'sysad')
			{
				$data['user_admin'] = $user['name'];
			}
			else
			{
				redirect('','refresh');
			}
			
			unset($data['submit']);
			$register = $this->records_model->register('emergency_room', $data);

			if($register)
			{
				$this->session->set_flashdata('result', 'Succesfully registered ER Card.');
				redirect('records/emerroom', 'refresh');
			}
			else
			{
				$this->form_validation->set_message('register', 'Something is wrong');
				return false;
			}
		}
	}
}
?>