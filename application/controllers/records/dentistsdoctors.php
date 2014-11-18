<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Dentistsdoctors extends CI_Controller
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
		// $loadedViews = array(
		// 					'records/records_header_view' => $this->header_links,
		// 					'records/dentistsdoctors/dentistsdoctors_register_view' => NULL,
		// 					'records/dentistsdoctors/dentistsdoctors_view' => NULL
		// 					);
		// $this->load->template($loadedViews, $this->header_links);
		
		if($this->session->userdata('logged_in'))
		{
			redirect('','refresh');
		}
	}

	function view($id)
	{
		$result = $this->records_model->getRecordById('dentistsanddoctors', $id);
		// $clinics = $this->records_model->getRecordByField('clinics', 'dentistsanddoctors_id', $id);
						
		if($result)
		{
			foreach($result as $row)
			{
				// $row['clinics'] = $clinics;
				$loadedViews = array(
									// 'records/records_header_view' => $this->header_links,
									'records/dentistsdoctors/dentistsdoctors_view_dentistsdoctors_view' => $row
									);
				$this->load->template($loadedViews);
			}
		}
		else
		{
			$this->session->set_flashdata('result', '<b>Record not found, may be deleted or an error occured.</b>');
			redirect('records/affiliatedserviceprovider','refresh');
		}
	}

	function multiSelect()
	{
		// var_dump($_POST);
		// if($_POST['submit'] == 'VAT and TERMS')
		// {
		// 	$count = 0;
		// 	foreach($_POST['selMulti'] as $id)
		// 	{
		// 		$fields = array(
		// 					'terms' => '30',
		// 					'vat' => 'NON-VAT PF'
		// 					);
		// 		$updateTermsVaT = $this->records_model->updateMultiField('dentistsanddoctors','id',$id,$fields);
		// 		$count++;
		// 	}

		// 	if($updateTermsVat)
		// 	{
		// 		$this->session->set_flashdata('result','Successfully updated '.$count.' records.');
		// 		redirect('records/affiliatedserviceprovider', 'refresh');
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
				$delete = $this->records_model->delete('dentistsanddoctors', $id);
				$delete2 = $this->records_model->delete('clinics', $id, 'dentistsanddoctors_id');
				$count++;
			}
			if($delete)
			{
				$this->session->set_flashdata('result', 'Deleted '.$count.' record/s of Dentist and Doctors.<br>');
				redirect('records/affiliatedserviceprovider', 'refresh');
			}
		}

		if($data['submit'] == 'Update Status')
		{
			$count = 0;
			$status = $data['status'];
			foreach($data['selMulti'] as $id)
			{
				$update = $this->records_model->update('dentistsanddoctors', 'status', $status, $id);
				$count++;
			}
			if($update)
			{ //if successfully updated members
				$this->session->set_flashdata('result', 'Updated '.$count.' record/s of Dentist and Doctors.<br>');
				redirect('records/affiliatedserviceprovider', 'refresh');
			}
		}
	}
	
	function register()
	{
		//Validates inputs from user, checks for security flaws
		$this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('firstname', 'Firstname', 'trim|required|xss_clean');
		$this->form_validation->set_rules('middlename', 'Middlename', 'trim|required|xss_clean');
		$this->form_validation->set_rules('lastname', 'Lastname', 'trim|required|xss_clean');
		$this->form_validation->set_rules('specialization', 'Specialization', 'trim|required|xss_clean');
		$this->form_validation->set_rules('email','E-mail','trim|xss_clean');
		
		$this->form_validation->set_rules('date_accredited', 'Date Accredited', 'trim|required|xss_clean|valid_date');
		$this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');
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

			// $clinic_info = array(
			// 					'clinic_name', 'hospital_name',
			// 					'street_address', 'subdivision_village',
			// 					'barangay', 'city',
			// 					'province', 'region', 'clinic_sched'
			// 					);
			// foreach($data['street_address'] as $key => $value) {
			// 	foreach($clinic_info as $field) {
			// 		$clinics[$key][$field] = $data[$field][$key];
			// 	}
			// }
			// foreach($clinic_info as $key => $value) {
			// 	unset($data[$value]);
			// }

			$register = $this->records_model->register('dentistsanddoctors', $data);

			if($register)
			{
				$this->session->set_flashdata('result', '<b>Successfully registered Dentist/Doctor.</b>');
				redirect('records/affiliatedserviceprovider','refresh');
			}
			// if($register)
			// {
			// 	foreach($clinics as $key => $value)
			// 	{
			// 		$clinics[$key]['dentistsanddoctors_id'] = $register;
			// 		$registerClinics = $this->records_model->register('clinics', $clinics[$key]);
			// 	}
			// 	if($registerClinics)
			// 	{
			// 		$this->session->set_flashdata('result', '<b>Succesfully registered Dentist/Doctor.</b>');
			// 		redirect('records/affiliatedserviceprovider', 'refresh');
			// 	}
			// }
			else
			{
				$this->session->set_flashdata('result', '<b>Error in registering data, database error or duplicate data occured.</b>');
				redirect('records/affiliatedserviceprovider', 'refresh');
			}
		}
	}

	function delete($id)
	{
		$delete = $this->records_model->delete('dentistsanddoctors', $id);
		if($delete)
		{
			$delete2 = $this->records_model->delete('clinics', $id, 'dentistsanddoctors_id');
			if($delete)
			{ //if successfully deleted dentist/doctor, reroute to dentistsdoctors with flashdata
				$this->session->set_flashdata('result', 'Deleted 1 Dentist/Doctor.<br>');
				redirect('records/affiliatedserviceprovider', 'refresh');
			}
		}
	}

	function search()
	{
		$this->form_validation->set_rules('dentanddoc', 'Dentist And Doctors', 'trim|xss_clean');
		$this->form_validation->set_rules('limit', 'Limit', 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/affiliatedserviceprovider', 'refresh');
		}
		else
		{
			unset($_POST['submit']);
			$keyword = $_POST['dentanddoc'];
			$limit = $_POST['limit'];

			$result = $this->records_model->getRecord('dentistsanddoctors', $keyword, $limit);
			
			foreach($result as $key => $value)
			{
				$clinics = $this->records_model->getRecordByField('clinics', 'dentistsanddoctors_id', $value['id']);
				
				$clinic_info = array(
									'clinic_name', 'hospital_name',
									'street_address', 'subdivision_village',
									'barangay', 'city',
									'province', 'region','clinic_sched'
									);
				if($clinics)
				{
					foreach($clinics as $ckey => $cval)
					{
						foreach($clinic_info as $field)
						{
							$result[$key]['clinics'][$ckey][$field] = $cval[$field];
							// $result[$key]['clinics'] .= $cval[$field];
						}
					}
				}
				else
				{
					$result[$key]['clinics'] = NULL;
				}
				$data['dentistsdoctors'] = $result;
			}

			$loadedViews = array(
						'records/records_header_view' => $this->header_links,
						'records/affiliatedprovider/asp_register_view' => NULL,
						'records/affiliatedprovider/asp_search_view' => NULL,
						'records/dentistsdoctors/dentistsdoctors_results_view' => $data
						);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function update()
	{
		var_dump($_POST);
		die();
		$clinic_info = array(
								'clinic_name', 'hospital_name',
								'street_address', 'subdivision_village',
								'barangay', 'city',
								'province', 'region', 'clinic_sched'
								);
		foreach($data['street_address'] as $key => $value)
		{
			foreach($clinic_info as $field)
			{
				$clinics[$key][$field] = $data[$field][$key];
			}
		}

		foreach($clinic_info as $key => $value)
		{
			unset($data[$value]);
		}

		$register = $this->records_model->register('dentistsanddoctors', $data);

		if($register)
		{
			foreach($clinics as $key => $value)
			{
				$clinics[$key]['dentistsanddoctors_id'] = $register;
				$registerClinics = $this->records_model->register('clinics', $clinics[$key]);
			}

			if($registerClinics)
			{
				$this->session->set_flashdata('result', 'Succesfully registered dentist/doctor.<br>');
				redirect('records/dentistsdoctors/view', 'refresh');
			}
		}
		else
		{
			$this->form_validation->set_message('register', 'Something is wrong.<br>');
			return false;
		}
	}
}