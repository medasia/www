<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Hospclinic extends CI_Controller
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
				case 'accre':
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
		// $data['hospclinic'] = $this->records_model->getAllRecords('hospital');

		// $loadedViews = array(
		// 					'records/records_header_view' => $this->header_links,
		// 					'records/hospclinic/hospclinic_register_view' => NULL,
		// 					'records/hospclinic/hospclinic_view' => NULL,
		// 					'records/hospclinic/hospclinic_results_view' => $data
		// 					);
		// $this->load->template($loadedViews, $this->header_links);]
		if($this->session->userdata('logged_in'))
		{
			redirect('','refresh');
		}
	}

	function view($id)
	{
		$result = $this->records_model->getRecordById('hospital', $id);
		if($result)
		{
			foreach($result as $row)
			{
				$loadedViews = array(
									// 'records/records_header_view' => $this->header_links,
									'records/hospclinic/hospclinic_view_hospclinic_view' => $row
									);
				// $this->load->template($loadedViews, $this->header_links);
				$this->load->template($loadedViews);
			}
		}
		else
		{
			$this->session->set_flashdata('result','<b>Record not found, may be deleted or an error occured.</b>');
			redirect('records/affiliatedserviceprovider', 'refresh');
		}
	}
	
	function register()
	{
		//Validates inputs from user, checks for security flaws
		$this->form_validation->set_rules('name', 'Hospital name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('classification', 'Classifications', 'trim|required|xss_clean');
		$this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('branch', 'Branch', 'trim|xss_clean');

		$this->form_validation->set_rules('street_address', 'Street Address', 'trim|xss_clean');
		$this->form_validation->set_rules('subdivision_village', 'Subdivision/Village', 'trim|xss_clean');
		$this->form_validation->set_rules('barangay', 'Barangay', 'trim|xss_clean');
		$this->form_validation->set_rules('city', 'City', 'trim|xss_clean');
		$this->form_validation->set_rules('province', 'Province', 'trim|xss_clean');
		$this->form_validation->set_rules('region', 'Region', 'trim|xss_clean');
		
		$this->form_validation->set_rules('contact_person', 'Contact Person', 'trim|xss_clean');
		$this->form_validation->set_rules('contact_number', 'Contact Number', 'trim|xss_clean');
		$this->form_validation->set_rules('fax_number', 'Fax Number', 'trim|xss_clean');
		$this->form_validation->set_rules('email', 'E-mail', 'trim|xss_clean');

		$this->form_validation->set_rules('med_coor_name', 'Medical Coordinator Name', 'trim|xss_clean');
		$this->form_validation->set_rules('room', 'Room', 'trim|xss_clean');
		$this->form_validation->set_rules('schedule','Schedule', 'trim|xss_clean');
		$this->form_validation->set_rules('contact_no', 'Contact Number', 'trim|xss_clean');

		$this->form_validation->set_rules('med_coor_name_2', 'Medical Coordinator Name 2', 'trim|xss_clean');
		$this->form_validation->set_rules('room_2', 'Room 2', 'trim|xss_clean');
		$this->form_validation->set_rules('schedule_2', 'Schedule 2', 'trim|xss_clean');
		$this->form_validation->set_rules('contact_no_2', 'Contact Number 2', 'trim|xss_clean');

		$this->form_validation->set_rules('category', 'Category', 'trim|xss_clean');
		$this->form_validation->set_rules('date_accredited', 'Date Accredited', 'trim|xss_clean|valid_date');
		$this->form_validation->set_rules('status', 'Status', 'trim|xss_clean');
		$this->form_validation->set_rules('remarks', 'Remarks', 'trim|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{ //if validation had errors reroute to useraccounts with flashdata that contains the said errors
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/affiliatedserviceprovider', 'refresh');
		}
		else
		{
			$data = $_POST;
			$user = $this->session->userdata('logged_in');
			$data['user'] = $user['name'];
			unset($data['submit']);
			$register = $this->records_model->register('hospital', $data);

			if($register)
			{
				$this->session->set_flashdata('result', '<b>Succesfully registered Hospital/Clinic.</b>');
				redirect('records/affiliatedserviceprovider', 'refresh');
			}
			else
			{
				$this->session->set_flashdata('result', '<b>Error in registering data, database error or duplicate data occured.');
				redirect('records/affiliatedserviceprovider', 'refresh');
			}
		}
	}

	function multiSelect()
	{
		// var_dump($_POST);
		// if($_POST['submit'] == 'VAT AND TERMS')
		// {
		// 	$count =0;
		// 	foreach($_POST['selMulti'] as $id)
		// 	{
		// 		$field = array(
		// 				'terms' => '30',
		// 				'vat' => 'NON-VAT HOSP'
		// 					);
		// 		$updateTermsVAT = $this->records_model->updateMultiField('hospital','id',$id,$field);
		// 		$count++;
		// 	}
		// 	if($updateTermsVAT)
		// 	{
		// 		$this->session->set_flashdata('result','Succesfully updated '.$count.' records.');
		// 		redirect('records/affiliatedserviceprovider','refresh');
		// 	}
		// }
		// else
		// {
		$this->form_validation->set_rules('selMulti[]', 'Multiple Select', 'trim|required|xss_clean');
		$this->form_validation->set_rules('status','Status', 'trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			echo "INPUT ERROR: All fields are required!!";
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/affiliatedserviceprovider', 'refresh');
		}
		else
		{
			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/verify_password_view' => NULL
							);
			$this->load->template($loadedViews, $this->header_links);
		}
		// }
	}

	function multiVerified()
	{
		$data = $this->session->flashdata('data');

		if($data['submit'] == 'Delete')
		{
			unset($data['status']);
			$count = 0;
			foreach($data['selMulti'] as $id)
			{
				$delete = $this->records_model->delete('hospital', $id);
				$count++;
			}
			if($delete)
			{
				$this->session->set_flashdata('result', 'Deleted '.$count.' record/s of Hospitals.<br>');
				redirect('records/affiliatedserviceprovider', 'refresh');
			}
		}

		if($data['submit'] == 'Update Status')
		{
			// unset($data['delete']);
			$count = 0;
			$status = $data['status'];
			foreach($data['selMulti'] as $id)
			{
				$update = $this->records_model->update('hospital', 'status', $status, $id);
				$count++;
			}
			if($update)
			{ //if successfully updated members
				$this->session->set_flashdata('result', 'Updated '.$count.' record/s of Hospitals.<br>');
				redirect('records/affiliatedserviceprovider', 'refresh');
			}
		}
	}

	function optionalSearch()
	{
		$this->form_validation->set_rules('branch', 'Branch', 'trim|xss_clean');
		$this->form_validation->set_rules('address', 'Address', 'trim|xss_clean');
		$this->form_validation->set_rules('province', 'Province', 'trim|xss_clean');
		$this->form_validation->set_rules('region', 'Region', 'trim|xss_clean');
		$this->form_validation->set_rules('limit', 'Limit', 'trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/affiliatedserviceprovider');
		}
		else
		{
			unset($_POST['submit']);
			$branch = $_POST['branch'];
			$address = $_POST['address'];
			$province = $_POST['province'];
			$region = $_POST['region'];
			$limit = $_POST['limit'];

			$result = $this->records_model->getHospitalByField('hospital',$branch, $address, $province, $region, $limit);

			$data['hospclinic'] = $result; 
			$loadedViews = array(
						'records/records_header_view' => $this->header_links,
						'records/affiliatedprovider/asp_register_view' => NULL,
						'records/affiliatedprovider/asp_search_view' => NULL,
						'records/hospclinic/hospclinic_results_view' => $data
						);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function search()
	{
		$this->form_validation->set_rules('hospital', 'Hospitals', 'trim|xss_clean');
		$this->form_validation->set_rules('limit', 'Limit', 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/affiliatedserviceprovider', 'refresh');
		}
		else
		{
			unset($_POST['submit']);
			$keyword = $_POST['hospital'];
			$limit = $_POST['limit'];

			$result = $this->records_model->getRecord('hospital', $keyword, $limit);
			
			$data['hospclinic'] = $result;

			$loadedViews = array(
						'records/records_header_view' => $this->header_links,
						'records/affiliatedprovider/asp_search_view' => NULL,
						'records/affiliatedprovider/asp_register_view' => NULL,
						'records/hospclinic/hospclinic_results_view' => $data
						);
			$this->load->template($loadedViews, $this->header_links);
		}
	}
	
	function delete($id) {
		$delete = $this->records_model->delete('hospital', $id);
		if($delete) { //if successfully deleted hospical clinic, reroute to hospclinic with flashdata
			$this->session->set_flashdata('result', 'Deleted hospital/clinic.<br>');
			redirect('records/affiliatedserviceprovider', 'refresh');
		}
	}
}
?>