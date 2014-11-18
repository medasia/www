<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Members extends CI_Controller
{
	/**
	 * Constructor
	 * 
	 * Loads MODELS, LIBS and HELPERS needed for class
	 * Checks if a SESSION is set, if true, set header links, if false, reroute to login
	 */
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
	/**
	 * Function index
	 * 
	 * Loads default views
	 */
	function index()
	{
		if($this->session->userdata('logged_in'))
		{
			$session_data = $this->session->userdata('logged_in');
			$data = $session_data;
		}
		// var_dump($data);
		if($data['usertype'] == 'accounting')
		{
			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/members/members_view' => NULL,
							);
			$this->load->template($loadedViews,$this->header_links);
		}
		elseif($data['usertype'] == 'sysad'
			|| $data['usertype'] == 'admin_assoc'
			|| $data['usertype'] == 'claims')
		{
			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/members/members_register_view' => NULL,
							'records/members/members_view' => NULL,
							// 'records/members/members_results_view' => $data
							);
			$this->load->template($loadedViews, $this->header_links);
		}
		else
		{
			echo '<script>alert("You are not allowed to access this portion of the site!");</script>';
			redirect('','refresh');
		}
	}
	/**
	 * Function view
	 * 
	 * @access public
	 * @param String $patientId ID of patient to be viewed
	 */
	function view($patientId)
	{
		$result = $this->records_model->getRecordById('patient', $patientId);
		$patientCompIns = $this->records_model->getRecordByField('patient_company_insurance', 'patient_id',$patientId);

		$patientLOA = $this->records_model->getRecordByField('availments_test','patient_id',$patientId);
 		
		if($patientLOA)
		{
			foreach($patientLOA as $key => $value)
			{
				// var_dump($value);
				@$labs = $this->records_model->getRecordByField('lab_test_test', 'availments_id', $value['id']);
				@$labInfo = array(
						'lab_test','amount'
						);
				if($labs)
				{
					foreach($labs as $lkey => $lval)
					{
						foreach($labInfo as $field)
						{
							$patientLOA[$key]['lab_test_test'][$lkey][$field] = $lval[$field];
						}
					}
				}
				else
				{
					@$patientLOA[$key]['lab_test_test'] = NULL;
				}
				$patientLOA[$key]['benefits_in-out_patient'] = $this->records_model->getRecordByField('benefits_in-out_patient','availment_id',$value['id']);
				$patientLOA[$key]['benefits_others'] = $this->records_model->getRecordByField('benefits_others','availment_id',$value['id']);
				$patientLOA[$key]['benefits_others_as_charged'] = $this->records_model->getRecordByField('benefits_others_as_charged','availment_id',$value['id']);
			}
		}		
		
		foreach($patientCompIns as $pci)
		{
			$compIns = $this->records_model->getRecordById('company_insurance', $pci['company_insurance_id']);
		}

		if($result)
		{
			foreach($result as $row)
			{
				// $benefit_details = $this->records_model->getRecordsByThreeFields('benefits.benefitset_info','compins_id','level','cardholder_type',$compIns[0]['id'],$row['level'],$row['cardholder_type']);
				// $benefit_details = $this->records_model->getRecordsByTwoFields('benefits.benefitset_info','compins_id','level',$compIns[0]['id'],$row['level']);
				$benefit_details = $this->records_model->getRecordByMultiField('benefits.benefitset_info',
																			array(
																				'compins_id' => $compIns[0]['id'],
																				'level' => $row['level'],
																				'cardholder_type' => $row['cardholder_type']
																				));
				if($benefit_details == FALSE)
				{
					$benefit_details = $this->records_model->getRecordByFieldsAndLike('benefits.benefitset_info',
																			array(
																				'compins_id' => $compIns[0]['id'],
																				'level' => $row['level'],
																				'cardholder_type' => $row['cardholder_type']
																				));
				}
				$admission = $this->records_model->getRecordByField('admission_report_test','patient_id',$row['id']);
				foreach($admission as $akey => $avalue)
				{
					$specialist = $this->records_model->getRecordByMultiField('admission_specialist',
																		array(
																			'code'=>$avalue['code'],
																			'time_generated'=>$avalue['time_generated']));
					foreach($specialist as $splst)
					{
						$admission[$akey]['specialist'][] = $splst;
					}
				}
				$monitoring = $this->records_model->getRecordByField('monitoring','patient_id',$row['id']);

				if($benefit_details)
				{
					// $row['benefit_details'] = $benefit_details[0];
					
					foreach($benefit_details as $key => $value)
					{
						if(($value['cardholder_type'] == ucfirst(strtolower($row['cardholder_type']))) || $value['cardholder_type'] == 'Principal and Dependent')
						{
							$row['benefit_details'] = $benefit_details[$key];
						}
						// echo '<pre>';
						// var_dump($value);
						$condition = $this->records_model->getRecordByField('benefits.benefit_set_condition','condition_name',$value['condition_name']);
						if($condition)
						{
							$row['condition_details'] = $condition[0];
						}

						$exclusion = $this->records_model->getRecordByField('benefits.benefit_set_exclusion','exclusion_name',$value['exclusion_name']);
						if($exclusion)
						{
							$row['exclusion_details'] = $exclusion[0];
						}
					}
				}
				$overall_mbl = $this->records_model->getRecordByMultiField('benefits_overall_mbl',array('patient_id'=>$patientId));
				if($overall_mbl)
				{
					$row['remaining_overall_mbl'] = $overall_mbl[0]['remaining_overall_mbl'];
				}

				if($patientLOA)
				{
					$row['availments'] = $patientLOA;
				}

				if($admission)
				{
					$row['admission'] = $admission;
				}

				if($monitoring)
				{
					$row['monitoring'] = $monitoring;
				}
				$row['compins'] = $compIns[0];

				// var_dump($row);
				$loadedViews = array(
									// 'records/records_header_view' => $this->header_links,
									'records/members/members_view_patient_view' => $row
									);
				$this->load->template($loadedViews);
			}
		}
		else
		{
			$this->session->set_flashdata('results','<b>Record not found, may be deleted or an error occured.</b>');
			redirect('records/members', 'refresh');
		}
	}
	
	function register() {
		//Validates inputs from user, checks for security flaws
		$this->form_validation->set_rules('firstname', 'Firstname', 'trim|required|xss_clean');
		$this->form_validation->set_rules('middlename', 'Middlename', 'trim|required|xss_clean');
		$this->form_validation->set_rules('lastname', 'Lastname', 'trim|required|xss_clean');
		$this->form_validation->set_rules('dateofbirth', 'Date of Birth', 'trim|required|xss_clean|valid_date');
		$this->form_validation->set_rules('level', 'Level/Position', 'trim|required|xss_clean');
		$this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');
		$this->form_validation->set_rules('declaration_date', 'Date of Declaration', 'trim|required|xss_clean|valid_date');
		$this->form_validation->set_rules('start', 'Start Date', 'trim|required|xss_clean|valid_date');
		$this->form_validation->set_rules('end', 'End Date', 'trim|required|xss_clean|valid_date');
		$this->form_validation->set_rules('remarks', 'Remarks', 'trim|xss_clean');
		$this->form_validation->set_rules('cardholder_type', 'Cardholder Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('company_insurance_id', 'Company-Insurance', 'trim|required|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{ //if validation had errors reroute to useraccounts with flashdata that contains the said errors
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/members', 'refresh');
		}
		else
		{
			$data = $_POST;
			$user = $this->session->userdata('logged_in');
			$data['user'] = $user['name'];
			$data['age'] = computeAge($data['dateofbirth']);
			// if($date['cardholder_type'] == 'principal')
			$data['cardholder_type'] == 'PRINCIPAL'? $data['cardholder'] = $data['lastname'].', '.$data['firstname'].' / '.$data['firstname'] : $data['cardholder']=$data['cardholder'];
			//var_dump($data['cardholder']);
			$company_insurance_id = $data['company_insurance_id'];
			unset($data['submit']); unset($data['company_insurance']); unset($data['company_insurance_id']);
			
			$register = $this->records_model->register('patient', $data);

			if($register)
			{
				// add to patient-company-insurance
				$data2 = array(
								'patient_id' => $register,
								'company_insurance_id' => $company_insurance_id
							);

				$registerCompIns = $this->records_model->register('patient_company_insurance', $data2);

				if($registerCompIns)
				{
					$this->session->set_flashdata('result', '<b>Member/s successfully added.</b>');
					redirect('records/members', 'refresh');
				}
			}
			else
			{
				$this->session->set_flashdata('result', '<b>Error in registering data, database error or duplicate data may occured.</b>');
				redirect('records/members', 'refresh');
			}
		}
	}

	function multiSelect()
	{
		// var_dump($_POST);
		$this->form_validation->set_rules('selMulti[]', 'Multiple Select', 'trim|required|xss_clean');
		$this->form_validation->set_rules('status','Status', 'trim|xss_clean');
		$data = $_POST;
		@$compins_id = $_POST['compins_id'];
		// echo '<pre>';
		// var_dump($data);

		if($this->form_validation->run() == FALSE)
		{
			if(isset($compins_id))
			{
				echo "INPUT ERROR: All fields are required!!";
				$this->session->set_flashdata('result', validation_errors());
				redirect('records/compins/members/'.$compins_id, 'refresh');
			}
			else
			{
				echo "INPUT ERROR: All fields are required!!";
				$this->session->set_flashdata('result', validation_errors());
				redirect('records/members', 'refresh');
			}	
		}
		else
		{
			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/verify_password_view' => $data
							);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function multiVerified()
	{	
		// ini_set('error_reporting', E_ALL);
		// echo '<pre>';
		$data = $this->session->flashdata('data');
		// var_dump($data);

		@$compins_id = $data['compins_id'];

		if($data['submit'] == 'Delete')
		{
			unset($data['status'],$data['start'],$data['end']);
			$count = 0;
			foreach($data['selMulti'] as $id)
			{
				$delete = $this->records_model->delete('patient', $id);
				$delete2 = $this->records_model->deleteByField('patient_company_insurance','patient_id',$id);
				$count++;
			}
			if($delete)
			{
				if(isset($compins_id))
				{
					$id = $compins_id;
					$this->session->set_flashdata('result', 'Deleted '.$count.' patient.');
					redirect('records/compins/members/'.$id, 'refresh');
				}
				else
				{
					$this->session->set_flashdata('result', 'Deleted '.$count.' patient.');
					redirect('records/members', 'refresh');
				}				
			}
		}

		if($data['submit'] == 'Update Status')
		{
			$count = 0;
			$status = $data['status'];
			foreach($data['selMulti'] as $id)
			{
				if(($data['start'] != '' && $data['end'] != '') && $data['status'] == 'ACTIVE')
				{
					$activeWithDate = array(
									'status' => $data['status'],
									'start' => $data['start'],
									'end' => $data['end']
										);
					$updateMultiField = $this->records_model->updateMultiField('patient','id',$id,$activeWithDate);
					$count++;
				}
				else
				{
					$update = $this->records_model->update('patient','status',$status,$id);
					$count++;
				}
			}
			if($update || $updateMultiField)
			{
				if(isset($compins_id))
				{
					$id = $compins_id;
					$this->session->set_flashdata('result', 'Updated Patient.');
					redirect('records/compins/members/'.$id, 'refresh');
				}
				else
				{
					$this->session->set_flashdata('result', 'Updated Patient.');
					redirect('records/members', 'refresh');
				}
			}
		}
	}

	function delete($id) {
		$delete = $this->records_model->delete('patient', $id);
		if($delete) {
			$delete2 = $this->records_model->delete('patient_company_insurance', $id, 'patient_id');
			if($delete) { //if successfully deleted patient, reroute to Members with flashdata
				$this->session->set_flashdata('result', 'Deleted patient.');
				redirect('records/members', 'refresh');
			}
		}
	}

	function search()
	{
		$this->form_validation->set_rules('members', 'Members', 'trim|xss_clean');
		$this->form_validation->set_rules('limit', 'Limit', 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/members', 'refresh');
		}
		else
		{
			unset($_POST['submit']);
			$keyword = $_POST['members'];
			$limit = $_POST['limit'];

			$result = $this->records_model->getRecord('patient', $keyword, $limit);
			
			foreach($result as $key => $value)
			{
				$patientCompIns = $this->records_model->getRecordByField('patient_company_insurance', 'patient_id', $value['id']);

				foreach($patientCompIns as $pci)
				{
					$compIns = $this->records_model->getRecordById('company_insurance', $pci['company_insurance_id']);
					$result[$key]['compins'] = $compIns;
				}
				$data['patients'] = $result;
			}

			$loadedViews = array(
						'records/records_header_view' => $this->header_links,
						'records/members/members_register_view' => NULL,
						'records/members/members_view' => NULL,
						'records/members/members_results_view' => $data
						);
			$this->load->template($loadedViews, $this->header_links);
		}
	}
}
?>