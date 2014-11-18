<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class login extends CI_Controller {
	/**
	 * Constructor
	 * 
	 * Loads MODEL AND LIBS needed for class
	 */
	function __construct() {
		parent::__construct();
		$this->load->model('accounts_model','',TRUE);
		$this->load->library(array('form_validation', 'table'));
	}
	/**
	 * Function index
	 */
	function index() {
		//This method will have the credentials validation
		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');

		if($this->form_validation->run() == FALSE) {
			//Field validation failed.&nbsp; User redirected to login page
			$this->load->view('login_view');
		}else{
			//Go to private area
			redirect('', 'refresh');
		}
	}
	function check_database($password) {
		//Field validation succeeded.&nbsp; Validate against database
		$username = $this->input->post('username');
		//query the database
		$result = $this->accounts_model->login($username, $password);

		if($result) {
			$sess_array = array();
			foreach($result as $row) {
				switch($row->usertype){ //switches from usertype from fetched data
					//sets header links and records links to userdata session depending on usertype
					case 'sysad':
						$header_links = array(
											'Verifications' => 'verifications',
											'Operations' => 'operations',
											'Claims' => 'claims',
											'Accounting' => 'accounting',
											'Records' => 'records',
											'User Accounts' => 'useraccounts',
											'Summary' => 'summary'
											);
						$records_links = array(
											// 'Company and Insurance' => 'records/compins',
											'All Members' => 'records/members',
											'Accounts' => 'records/accounts',
								   // 			'Company' => 'records/company',
											// 'Insurance' => 'records/insurance',
											'Affiliated Service Provider' => 'records/affiliatedserviceprovider',
											'Affiliated Service Accounts' => 'records/hospaccnt',
											'Diagnosis' => 'records/diagnosis',
											// 'Hospital and Clinic' => 'records/hospclinic',
											// '<i>Dentists and Doctors</i>' => 'records/dentistsdoctors',
											// 'Hospital Accounts' => 'records/hospaccnt',
											'Benefits' => 'records/benefits',
											'Emergency Room' => 'records/emerroom',
											// 'Calendar' => 'records/calendar',
											'Upload History' => 'records/uphist'
											);
					break;

					case 'admin_assoc':
						$header_links = array(
											// '<i>Verifications</i>' => 'verifications',
											'Operations' => 'operations',
											// '<i>Claims</i>' => 'claims',
											// '<i>Summary</i>' => 'summary',
											'Records' => 'records',
											'User Accounts' => 'useraccounts'
											);
						$records_links = array(
											// 'Company and Insurance' => 'records/compins',
											'All Members' => 'records/members',
											'Accounts' => 'records/accounts',
											// 'Company' => 'records/company',
											// 'Insurance' => 'records/insurance',
											// 'Affiliated Service Provider' => 'records/affiliatedserviceprovider'
											// 'Hospital and Clinic' => 'records/hospclinic',
											// '<i>Dentists and Doctors</i>' => 'records/dentistsdoctors',
											// 'Hospital Accounts' => 'records/hospaccnt',
											'Benefits' => 'records/benefits',
											'Emergency Room' => 'records/emerroom',
											// 'Calendar' => 'records/calendar',
											// 'Upload History' => 'records/uphist'
											);
					break;

					case 'ops':
						$header_links = array(
											'Verifications' => 'verifications',
											'Operations' => 'operations',
											// '<i>Claims</i>' => 'claims',
											'Records' => 'records/benefits',
											'User Accounts' => 'useraccounts',
											'Summary' => 'summary'
											);
						$records_links = array(
											// 'Company and Insurance' => 'records/compins',
											// 'All Members' => 'records/members',
											// 'Accounts' => 'records/accounts',
											// 'Company' => 'records/company',
											// 'Insurance' => 'records/insurance',
											// 'Hospital and Clinic' => 'records/hospclinic',
											// '<i>Dentists and Doctors</i>' => 'records/dentistsdoctors',
											// 'Hospital Accounts' => 'records/hospaccnt',
											// 'Diagnosis' => 'records/diagnosis',
											'Benefits' => 'records/benefits',
											// 'Emergency Room' => 'records/emerroom',
											// 'Calendar' => 'records/calendar',
											// 'Upload History' => 'records/uphist'
											);
					break;

					case 'claims':
						$header_links = array(
											// '<i>Verifications</i>' => 'verifications',
											// '<i>Operations</i>' => 'operations',
											'Claims' => 'claims',
											// 'Records' => 'records',
											'User Accounts' => 'useraccounts',
											'Summary' => 'summary'
											);
						$records_links = array(
											// 'Company and Insurance' => 'records/compins',
											'All Members' => 'records/members',
											'Accounts' => 'records/accounts',
											// 'Company' => 'records/company',
											// 'Insurance' => 'records/insurance',
											// 'Affiliated Service Provider' => 'records/affiliatedserviceprovider',
											// 'Hospital and Clinic' => 'records/hospclinic',
											// '<i>Dentists and Doctors</i>' => 'records/dentistsdoctors',
											// 'Hospital Accounts' => 'records/hospaccnt',
											'Benefits' => 'records/benefits',
											'Emergency Room' => 'records/emerroom',
											// 'Calendar' => 'records/calendar',
											// 'Upload History' => 'records/uphist'
											);
					break;

					case 'accre':
						$header_links = array(
											// '<i>Verifications</i>' => 'verifications',
											// '<i>Operations</i>' => 'operations',
											// '<i>Claims</i>' => 'claims',
											'Records' => 'records/hospclinic',
											'User Accounts' => 'useraccounts',
											'Summary' => 'summary'
											);
						$records_links = array(
											// 'Company and Insurance' => 'records/compins',
											// 'All Members' => 'records/members',
											// 'Accounts' => 'records/accounts',
											// 'Company' => 'records/company',
											// 'Insurance' => 'records/insurance',
											'Affiliated Service Provider' => 'records/affiliatedserviceprovider',
											// 'Hospital and Clinic' => 'records/hospclinic',
											// '<i>Dentists and Doctors</i>' => 'records/dentistsdoctors',
											// 'Hospital Accounts' => 'records/hospaccnt',
											// '<i>Benefits</i>' => 'records/benefits',
											// 'Emergency Room' => 'records/emerroom',
											// 'Calendar' => 'records/calendar',
											// 'Upload History' => 'records/uphist'
											);
					break;

					case 'accounting':
						$header_links = array(
											// '<i>Verifications</i>' => 'verifications',
											// '<i>Operations</i>' => 'operations',
											// '<i>Claims</i>' => 'claims',
											'Accounting' => 'accounting',
											'Records' => 'records/hospaccnt',
											'User Accounts' => 'useraccounts',
											'Summary' => 'summary',
											);
						$records_links = array(
											// 'Company and Insurance' => 'records/compins',
											'All Members' => 'records/members',
											'Accounts' => 'records/accounts',
											// 'Company' => 'records/company',
											// 'Insurance' => 'records/insurance',
											// 'Affiliated Service Provider' => 'records/affiliatedserviceprovider',
											// 'Hospital and Clinic' => 'records/hospclinic',
											// '<i>Dentists and Doctors</i>' => 'records/dentistsdoctors',
											'Affiliated Service Accounts' => 'records/hospaccnt'
											// '<i>Benefits</i>' => 'records/benefits',
											// 'Emergency Room' => 'records/emerroom',
											// 'Calendar' => 'records/calendar',
											// 'Upload History' => 'records/uphist'
											);
					break;
				}
				$sess_array = array(
									'id' => $row->id,
									'username' => $row->username,
									'name' => $row->name,
									'access' => $row->access,
									'usertype' => $row->usertype,
									'header_links' => $header_links,
									'records_links'=> $records_links
									);
				//set userdata to CI's session class
				$this->session->set_userdata('logged_in', $sess_array);
			}
			return TRUE;
		} else {
			$this->form_validation->set_message('check_database', 'Invalid username or password');
			return false;
		}
	}

	function verify_password()
	{
		var_dump($_POST);
		$this->form_validation->set_rules('password','Password', 'trim|xss_clean|required');

		$session_data = $this->session->userdata('logged_in');
		$data = $session_data;
		
		$password = $this->input->post('password');
		$result = $this->accounts_model->login($data['username'], $password);
		$location = $_POST['location'];

		if($_POST['submit'] == 'Delete')
		{
			unset($_POST['start'],$_POST['end'],$_POST['status']);
		}
		// var_dump($result);
		
		if($result)
		{
			// echo 'CORRECT';
			$this->session->set_flashdata('data',$_POST);
			redirect($location.'/multiVerified', 'refresh');
			// var_dump($_POST);
		}
		else
		{
			echo "INCORRECT PASSWORD!!!";
			$this->form_validation->set_message('verify_password', 'Incorrect Password.');
			redirect($location.'/multiSelect', 'refresh');
		}
	}

	function verify_pass()
	{
		$data['id'] = $_POST['id'];
		$data['field'] = $_POST['field'];
		$password = $_POST['password'];
		$location = $_POST['location'];
		$this->form_validation->set_rules('password','Password', 'trim|xss_clean|required');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_erorr());
			redirect($location, 'refresh');
		}
		else
		{
			$session_data = $this->session->userdata('logged_in');

			$verify = $this->accounts_model->login($session_data['username'],$password);

			if($verify)
			{
				$this->session->set_flashdata('data',$data);
				redirect($location.'/proceedDelete','refresh');
			}
			else
			{
				$this->session->set_flashdata('result','<b>Incorrect Password, Deletion Failed.');
				redirect($location,'refresh');
			}
		}
	}
}
?>