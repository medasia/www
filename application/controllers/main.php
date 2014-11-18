<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Main extends CI_Controller {
	/**
	 * Constructor
	 * 
	 * Loads LIB and HELPER needed for class
	 */
	function __construct() {
		parent::__construct();
		$this->load->library('table');
		$this->load->helper('form');
	}
	/**
	 * Function index
	 * 
	 * Checks if userdata exists on session
	 * if true, reroute to designated page
	 * 		i.e. SysAds redirects to useraccounts
	 * 			 Claims redirects to claims
	 * if false, load LOGIN view
	 */
	function index() {
		if($this->session->userdata('logged_in')) {
			$session_data = $this->session->userdata('logged_in');
			$data = $session_data;
			// switch case here for redirection depending on department and account type
			switch($data['usertype']){
				case 'sysad':
					redirect('useraccounts', 'refresh');
				break;
				case'admin_assoc':
					redirect('records', 'refresh');
				break;
				case'ops':
					redirect('operations', 'refresh');
				break;
				case'claims':
					redirect('useraccounts', 'refresh');
				break;
				case'accre':
					redirect('records/affiliatedserviceprovider', 'refresh');
				break;
				case'accounting':
					redirect('records/hospaccnt');
				break;
			}
		} else {
			//If no session, redirect to login page
			$this->load->view('login_view');
		}
	}
	/**
	 * Function logout
	 * 
	 * Logouts user, unsets userdate on CI's session class and destroys session then redirects to root dir
	 */
	function logout() {
		$this->session->unset_userdata('logged_in');
		session_destroy();
		redirect('', 'refresh');
	}
}
?>