<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Useraccounts extends CI_Controller
{
	/**
	 * Constructor
	 * 
	 * Loads MODEL, LIBS and HELPER needed for class
	 * Checks if a SESSION is set, if true, set header links, if false, reroute to login
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('useraccounts_model','',TRUE);
		$this->load->model('accounts_model','',TRUE);
		$this->load->library(array('table', 'form_validation'));
		$this->load->helper('form');
		if($this->session->userdata('logged_in'))
		{
			//set header links depending on logged in users in userdata session
			$this->header_links = $this->session->userdata('logged_in');
		}
		else
		{
			//If no session, redirect to login page
			redirect('../', 'refresh');
		}
	}
	/**
	 * Function index
	 * 
	 * Loads default VIEWS
	 */
	function index()
	{
		if($this->session->userdata('logged_in'))
		{
			$session_data = $this->session->userdata('logged_in');
			$data = $session_data;

			if($data['usertype'] == 'sysad')
			{
				$result['users_new'] = $this->useraccounts_model->getAllRecords('users');
				$loadedViews = array(
							'useraccounts/useraccounts_register_view' => NULL,
							'useraccounts/useraccounts_view' => $result
							);
				$this->load->template($loadedViews, $this->header_links);
			}
			else
			{
				$id = $data['id'];
				$loadedViews = array(
								'useraccounts/useraccounts_changepass_view' => $id
								);
				$this->load->template($loadedViews, $this->header_links);
			}	
		}
	}
	/**
	 * Function register
	 * 
	 * Registers new user
	 *
	 * @access public
	 * @return boolean
	 */
	function register() {
		//Validates inputs from user, checks for security flaws
		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
		$this->form_validation->set_rules('access', 'Access', 'trim|required|xss_clean');
		$this->form_validation->set_rules('usertype', 'Usertype', 'trim|required|xss_clean');
		
		if($this->form_validation->run() == FALSE) { //if validation had errors reroute to useraccounts with flashdata that contains the said errors
			$this->session->set_flashdata('result', validation_errors());
			redirect('useraccounts', 'refresh');
		} else {
			$data = $_POST;
			unset($data['submit']);
			$register = $this->useraccounts_model->register('users', $data);

			if($register) {
				$this->session->set_flashdata('result', 'Succesfully registered user.');
				redirect('useraccounts', 'refresh');
			} else {
				$this->form_validation->set_message('register', 'Something is wrong');
				return false;
			}
		}
	}
	/**
	 * Function edit
	 * 
	 * Function for displaying view for editing users
	 * 
	 * @access public
	 * @param String $id ID of user to be edited
	 */
	function edit($id)
	{
		$result = $this->useraccounts_model->getRecordById('users', $id);

		if($result)
		{ //if DB returns true, display view with data of user fetched from DB
			foreach($result as $row)
			{
				$loadedViews = array(
									'useraccounts/useraccounts_edit_view' => $row
									);
				$this->load->template($loadedViews, $this->header_links);
			}
		}
	}
	/**
	 * Function update
	 * 
	 * Updates user with inputs from user
	 * 
	 * @access public
	 * @param String $id ID of user to be updated
	 * @return boolean
	 */
	function update($id) {
		//Validates inputs from user, checks for security flaws
		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'trim|xss_clean');
		$this->form_validation->set_rules('access', 'Access', 'trim|required|xss_clean');
		$this->form_validation->set_rules('usertype', 'Usertype', 'trim|required|xss_clean');
		

		if($this->form_validation->run() == FALSE)
		{ //if validation had errors reroute to useraccounts with flashdata that contains the said errors
			$this->session->set_flashdata('result', validation_errors());
			redirect('useraccounts/edit/'.$this->uri->segment(3), 'refresh');
		}
		else
		{
			$data = $_POST;
			if($data['password'] == '') unset($data['password']);
			$update = $this->useraccounts_model->update('users', $id, $data);
			if($update)
			{
				$this->session->set_flashdata('result', 'Succesfully edited user.');
				redirect('useraccounts', 'refresh');
			}
			else
			{
				$this->form_validation->set_message('update', 'Something is wrong');
				return false;
			}
		}
	}
	/**
	 * Function delete
	 * 
	 * Deletes user from DB
	 * 
	 * @access public
	 * @param String $id ID of user to be deleted
	 */
	function delete($id)
	{
		$delete = $this->useraccounts_model->delete('users', $id);
		if($delete)
		{ //if successfully deleted user, reroute to Useraccounts with flashdata
			$this->session->set_flashdata('result', 'Deleted user.');
			redirect('useraccounts', 'refresh');
		}
	}

	function changePass($id)
	{
		$this->form_validation->set_rules('password', 'Old Password', 'trim|required|xss_clean');
		$this->form_validation->set_rules('new_pass', 'New Password', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ver_pass', 'Verify Password', 'trim|required|xss_clean');

		$session_data = $this->session->userdata('logged_in');
		$dataSess = $session_data;

		$password = $this->input->post('password');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_error());
			redirect('useraccounts', 'refresh');
		}
		else
		{
			if($_POST['new_pass'] == $_POST['ver_pass'])
			{
				$result = $this->accounts_model->login($dataSess['username'], $password);

				if($result)
				{
					unset($_POST['old_pass']);
					$data['password'] = $_POST['new_pass'];
					$changePass = $this->useraccounts_model->update('users', $id, $data);

					if($changePass)
					{
						$this->session->set_flashdata('result', 'Password successfully changed!');
						echo '<script>alert("Password successfully updated!!");</script>';
						redirect('useraccounts','refresh');
					}
					else
					{
						$this->form_validation->set_message('update', 'Something is wrong!');
						echo "Something is wrong!";
						redirect('useraccounts', 'refresh');
						return false;
					}
				}
				else
				{
					$this->form_validation->set_message('update', 'Password is incorrect!');
					echo "Password is incorrect";
					redirect('useraccounts', 'refresh');
					return false;
				}
			}
			else
			{
				$this->form_validation->set_message('update', 'New Password and Verify Password are not the same.');
				echo "New Password and Verify Password are not the same.";
				redirect('useraccounts','refresh');
				return false;
			}
		}
	}

	function check_exist($table,$field)
	{
		$term = $this->input->post('term');
		$data = array($field => $term);
		$result = $this->useraccounts_model->getRecordByField($table,$data);
		if($result)
		{
			echo json_encode(FALSE);
		}
		else
		{
			echo json_encode(TRUE);
		}
	}
}
?>