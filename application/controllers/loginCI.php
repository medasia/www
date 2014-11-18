<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class LoginCI extends CI_Controller {
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
											'<i>Verifications</i>' => 'verifications',
											'<i>Operations</i>' => 'operations',
											'<i>Claims</i>' => 'claims',
											'<i>Summary</i>' => 'summary',
											'Records' => 'records',
											'User Accounts' => 'useraccounts'
											);
						$records_links = array(
											'Company and Insurance' => 'records/compins',
											'All Members' => 'records/members',
											'Company' => 'records/company',
											'Insurance' => 'records/insurance',
											'Hospital and Clinic' => 'records/hospclinic',
											'<i>Dentists and Doctors</i>' => 'records/dentistsdoctors',
											'Hospital Accounts' => 'records/hospaccnt',
											'<i>Benefits</i>' => 'records/benefits',
											'Emergency Room' => 'records/emerroom',
											// 'Calendar' => 'records/calendar',
											'Upload History' => 'records/uphist'
											);
					break;

					case 'admin_assoc':
						$header_links = array(
											// '<i>Verifications</i>' => 'verifications',
											'<i>Operations</i>' => 'operations',
											// '<i>Claims</i>' => 'claims',
											// '<i>Summary</i>' => 'summary',
											'Records' => 'records',
											'User Accounts' => 'useraccounts'
											);
						$records_links = array(
											'Company and Insurance' => 'records/compins',
											'All Members' => 'records/members',
											'Company' => 'records/company',
											'Insurance' => 'records/insurance',
											// 'Hospital and Clinic' => 'records/hospclinic',
											// '<i>Dentists and Doctors</i>' => 'records/dentistsdoctors',
											// 'Hospital Accounts' => 'records/hospaccnt',
											'<i>Benefits</i>' => 'records/benefits',
											'Emergency Room' => 'records/emerroom',
											// 'Calendar' => 'records/calendar',
											// 'Upload History' => 'records/uphist'
											);
					break;

					case 'ops':
						$header_links = array(
											'<i>Verifications</i>' => 'verifications',
											'<i>Operations</i>' => 'operations',
											// '<i>Claims</i>' => 'claims',
											'<i>Summary</i>' => 'summary',
											'Records' => 'records/benefits',
											'User Accounts' => 'useraccounts'
											);
						$records_links = array(
											// 'Company and Insurance' => 'records/compins',
											// 'All Members' => 'records/members',
											// 'Company' => 'records/company',
											// 'Insurance' => 'records/insurance',
											// 'Hospital and Clinic' => 'records/hospclinic',
											// '<i>Dentists and Doctors</i>' => 'records/dentistsdoctors',
											// 'Hospital Accounts' => 'records/hospaccnt',
											'<i>Benefits</i>' => 'records/benefits',
											// 'Emergency Room' => 'records/emerroom',
											// 'Calendar' => 'records/calendar',
											// 'Upload History' => 'records/uphist'
											);
					break;

					case 'claims':
						$header_links = array(
											// '<i>Verifications</i>' => 'verifications',
											// '<i>Operations</i>' => 'operations',
											'<i>Claims</i>' => 'claims',
											'<i>Summary</i>' => 'summary',
											// 'Records' => 'records',
											'User Accounts' => 'useraccounts'
											);
						$records_links = array(
											'Company and Insurance' => 'records/compins',
											'All Members' => 'records/members',
											'Company' => 'records/company',
											'Insurance' => 'records/insurance',
											// 'Hospital and Clinic' => 'records/hospclinic',
											// '<i>Dentists and Doctors</i>' => 'records/dentistsdoctors',
											// 'Hospital Accounts' => 'records/hospaccnt',
											'<i>Benefits</i>' => 'records/benefits',
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
											'<i>Summary</i>' => 'summary',
											'Records' => 'records',
											'User Accounts' => 'useraccounts'
											);
						$records_links = array(
											'Company and Insurance' => 'records/compins',
											// 'All Members' => 'records/members',
											// 'Company' => 'records/company',
											// 'Insurance' => 'records/insurance',
											'Hospital and Clinic' => 'records/hospclinic',
											'<i>Dentists and Doctors</i>' => 'records/dentistsdoctors',
											// 'Hospital Accounts' => 'records/hospaccnt',
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
}
?>