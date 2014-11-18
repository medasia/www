<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Verifications extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		// $this->output->enable_profiler(TRUE); PROFILER
		$this->load->model('verifications_model','',TRUE);
		$this->load->library(array('table', 'form_validation', 'code'));
		$this->load->helper(array('benefit_helper','benefit_remarks_helper','html'));

		if($this->session->userdata('logged_in'))
		{
			//set header links depending on logged in users in userdata session
			$this->header_links = $this->session->userdata('logged_in');

			$session_data = $this->session->userdata('logged_in');

			switch($session_data['usertype'])
			{
				case 'sysad':
				case 'ops':
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
			$valid = $session_data;

			switch($valid['usertype'])
			{
				case 'sysad':
				case 'ops':
						@$date = mdate('%Y-%m-%d', now());
						@$result = $this->verifications_model->getRecordsByDate('availments_test',$date);
		
						// @$result = $this->verifications_model->getAllRecords('availment_test');
						// var_dump($result);
		
						foreach($result as $key => $value)
						{
							@$labs = $this->verifications_model->getRecordByField('lab_test_test', 'availments_id', $value['id']);
							@$labInfo = array(
									'lab_test','amount'
									);
							if($labs)
							{
								foreach($labs as $lkey => $lval)
							{
							foreach($labInfo as $field)
							{
								@$result[$key]['lab_test_test'][$lkey][$field] = $lval[$field];
							}
						}
					}
					else
					{
						@$result[$key]['lab_test_test'] = NULL;
					}
						@$result[$key]['benefits_in-out_patient'] = $this->verifications_model->getRecordByField('benefits_in-out_patient','availment_id',$value['id']);
						@$result[$key]['benefits_others'] = $this->verifications_model->getRecordByField('benefits_others','availment_id',$value['id']);
						@$result[$key]['benefits_others_as_charged'] = $this->verifications_model->getRecordByField('benefits_others_as_charged','availment_id',$value['id']);
						$result[$key]['diagnosis'] = $this->verifications_model->getRecordByField('availments_diagnosis','code',$value['code']);
						$result[$key]['specialist'] = $this->verifications_model->getRecordByField('admission_specialist','code',$value['code']);
						$result[$key]['compins_notes'] = $this->verifications_model->getRecordByMultiField('company_insurance',
																							array(
																								'company' => $value['company_name'],
																								'insurance' => $value['insurance_name']
																								));
						@$result[$key]['illness'] = $this->verifications_model->getRecordByField('patient_illness','loa_code',$value['code']);
						@$data['result'] = $result;
					}
						// echo '<pre>';
						// var_dump($data);
					@$loadedViews = array(
						// 'verifications/verifications_register_view' => NULL,
						'verifications/verifications_members_view' => @$data
						);
					@$this->load->template($loadedViews, $this->header_links);
				break;

				default:
					echo '<script>alert("You are not allowed to access this portion of the site!");</script>';
					redirect('','refresh');
			}
		}
	}

	function newLOA($id)
	{
		$result = $this->verifications_model->getRecordById('patient', $id);
		$patientCompIns = $this->verifications_model->getRecordByField('patient_company_insurance', 'patient_id',$id);
		$compIns = $this->verifications_model->getRecordById('company_insurance', $patientCompIns[0]['company_insurance_id']);
		$comCode = $this->verifications_model->getRecordByField('company', 'name', $compIns[0]['company']);
		$insCode = $this->verifications_model->getRecordByField('insurance', 'name', $compIns[0]['insurance']);

		//BENEFITS DETAILS AND INFO
		$benefitset_info = $this->verifications_model->getRecordByMultiField('benefits.benefitset_info',
																		array(
																			'compins_id' => $patientCompIns[0]['company_insurance_id'],
																			'level' => $result[0]['level'],
																			'cardholder_type' => $result[0]['cardholder_type']));

		if($benefitset_info == FALSE)
		{
			$benefitset_info = $this->verifications_model->getRecordByFieldsAndLike('benefits.benefitset_info',
																		array(
																			'compins_id' => $patientCompIns[0]['company_insurance_id'],
																			'level' => $result[0]['level'],
																			'cardholder_type' => $result[0]['cardholder_type']));
		}

		foreach($benefitset_info as $key => $value)
		{
			@$member = $this->verifications_model->getRecordByField('benefits.'.str_replace(" ","_",$value['benefit_set_name']), 'patient_id', $id);
			if($member)
			{
				$benefit_set_name = $value['benefit_set_name'];
				$benefit_set_id = $value['id'];
				@$benefitset_details = $this->verifications_model->getRecordByField('benefits.benefitset_details', 'benefit_set_id', $value['id']);
			}
		}

		foreach($benefitset_details as $key => $value)
		{
			$availment_type[$value['benefit_type']] = $value['benefit_type'];
		}
		
		//BENEFITS
		$result[0]['benefitset_info'] = $benefitset_info[0];
		$result[0]['options'] = $availment_type;
		$result[0]['benefit_name'] = $benefit_set_name;
		$result[0]['benefit_set_id'] = $benefit_set_id;

		$result[0]['compins'] = $compIns[0];
		$result[0]['company_code'] = $comCode[0];
		$result[0]['insurance_code'] = $insCode[0];
		
		// echo '<pre>';
		// var_dump($result);
		$loadedViews = array(
							'verifications/verifications_register_view' => $result[0]
							);
		$this->load->template($loadedViews, $this->header_links);
	}

	function registerLOA()
	{
		unset($_POST['submit']);
		$this->form_validation->set_rules('hospital_name','Hospital Name','trim|required|xss_clean');
		$this->form_validation->set_rules('hospital_branch','Hospital Branch','trim|xss_clean');
		// $this->form_validation->set_rules('chief_complaint','Chief Complaint','trim|required|xss_clean');
		$this->form_validation->set_rules('availment_type', 'Availment Type','trim|required|xss_clean');
		$this->form_validation->set_rules('patient_name','Patient Name','trim|required|xss_clean');
		$this->form_validation->set_rules('patient_id','Patient ID','trim|required|xss_clean');
		$this->form_validation->set_rules('company_name','Company Name','trim|required|xss_clean');
		$this->form_validation->set_rules('insurance_name','Insurance Name','trim|required|xss_clean');
		$this->form_validation->set_rules('company_code','Company Code', 'trim|required|xss_clean');
		$this->form_validation->set_rules('insurance_code','Insurance Code', 'trim|required|xss_clean');
		$this->form_validation->set_rules('principal_name','Principal Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('benefit_set_id','Benefit Set Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('date_encoded','Date Encoded', 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('operations','refresh');
		}
		else
		{
			$table = str_replace(" ","_",$_POST['benefit_name']);
			$key1 = $_POST['benefit_set_id'];
			$key2 = $_POST['availment_type'];
			$data = $_POST;

			if($key2 == 'In-Patient')
			{
				$benefit_type = 'IP';
			}
			elseif($key2 == 'Out-Patient')
			{
				$benefit_type = 'OP';
			}
			elseif($key2 = 'In and Out Patient')
			{
				$benefit_type = 'IP-OP';
			}

			if($key2 == 'In-Patient')
			{
				$data = $_POST;
				$data['diagnosis'] = array_values(array_unique($_POST['diagnosis']));

				$loadedViews = array(
								'verifications/verifications_admission_report_view' => $data
								);
				$this->load->template($loadedViews, $this->header_links);
			}
			else
			{
				$benefit_details = $this->verifications_model->getRecordByMultiField('benefits.benefitset_details',
																				array(
																					'benefit_set_id' => $key1,
																					'benefit_type' => $key2
																					)
																				);
				// var_dump($benefit_details);

				foreach($benefit_details as $key => $value)
				{
					$details[$value['registered_benefit']] = $this->verifications_model->getRecordByMultiField('benefits.basic_benefitss',
																										array(
																											'benefit_type' => $benefit_type,
																											'benefit_name' => $value['registered_benefit']
																											));
					$bm = benefit_remarks($value['registered_benefit']);
					if($bm)
					{
						$remarks = ' - '.$bm;
					}
					else
					{
						$remarks = '';
					}
					$benefit_name[$value['registered_benefit']] = $value['registered_benefit'].$remarks;
				}
				// var_dump($details);

				$data['options'] = $benefit_name;
				$data['diagnosis'] = array_values(array_unique($_POST['diagnosis']));
				// echo '<pre>';
				// var_dump($data);
				// var_dump($this->header_links);
				$loadedViews = array(
								'verifications/verifications_register_LOA_view' => $data
								);
				$this->load->template($loadedViews, $this->header_links);
			}
		}
	}

	function fillDetails()
	{
		unset($_POST['submit']);

		if($_POST['availment_type'] == 'In-Patient')
		{
			$benefit_type = 'IP';
		}
		elseif($_POST['availment_type'] == 'Out-Patient')
		{
			$benefit_type = 'OP';
		}
		elseif($_POST['availment_type'] == 'In and Out Patient')
		{
			$benefit_type = 'IP-OP';
		}

		if($_POST['availment_type'] == 'In-Patient' || $_POST['availment_type'] == 'In and Out Patient')
		{
			$this->form_validation->set_rules('benefit_name[]','Benefit Name', 'trim|required|xss_clean');

			if($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('result',validation_errors());
				redirect('operations','refresh');
			}
			else
			{
				$data = $_POST;
				unset($data['benefit_name']);
				$data['benefit_name'] = array_values(array_unique($_POST['benefit_name']));
				$benefit = $this->verifications_model->getRecordById('benefits.benefitset_info',$_POST['benefit_set_id']);
				$tableName = str_replace(" ", "_", $benefit[0]['benefit_set_name']);
				$data['benefit_set_name'] = $benefit[0]['benefit_set_name'];
				$data['plan_type'] = $benefit[0]['plan_type'];

				$tableDetails = $this->verifications_model->getRecordByField('benefits.'.$tableName,'patient_id',$_POST['patient_id']);
				$data['fields'] = $tableDetails[0];

				//OVERALL BENEFIT LIMIT
				$overall_mbl_exist = $this->verifications_model->getRecordByField('benefits_overall_mbl','patient_id',$_POST['patient_id']);
				if($overall_mbl_exist)
				{
					$data['fields']['maximum_benefit_limit'] = $overall_mbl_exist[0]['remaining_overall_mbl'];
				}

				if($_POST['availment_type'] == 'In-Patient')
				{
					if(isset($_POST['code']))
					{
						$admission = $this->verifications_model->getRecordByField('admission_report_test','code',$_POST['code']);
						$data['physician'] = $admission[0]['physician'];
					}
				}

				// CHECK IF BENEFIT LIMIT TYPE is PER ILLNESS, and if ILLNESS already exist in the Patient
				if(isset($_POST['benefit_limit_type']))
				{
					$illness_exist = $this->verifications_model->getRecordByMultiField('patient_illness',
																			array('patient_id'=>$data['patient_id'],
																				'illness'=>$data['illness']));
				}

				foreach($data['benefit_name'] as $key => $value)
				{
					$value_details = $this->verifications_model->getRecordByMultiField('benefits.basic_benefitss',
																				array(
																					'benefit_type' => $benefit_type,
																					'benefit_name' => $value
																					));

					@$remainingDetails[$key] = $this->verifications_model->getRecordsByTwoFieldsOrderByDesc('benefits_in-out_patient','patient_id','benefit_name',$_POST['patient_id'],$value);
				
					foreach($value_details as $dkey => $dvalue)
					{
						$details[$key][$dkey] = $dvalue['details'];
						$fieldValue[$key][$dkey] = $benefit_type."#".str_replace(" ","_", $dvalue['benefit_name'])."#".$dvalue['details'];

						//FIELDS IF patient HAS REMAINING DETAILS
						if($remainingDetails[$key])
						{
							//REMAINING FOR DAYS
							if(@$remainingDetails[$key][0]['remaining_days'] != 0 && $dvalue['details'] == 'DAYS')
							{
								@$fields_remaining[$benefit_type."#".str_replace(" ","_",$value)."#".str_replace(" ","_",$dvalue['details'])] = $remainingDetails[$key][0]['remaining_days'];
							}
							elseif(@$remainingDetails[$key][0]['remaining_days'] <= 0 && $dvalue['details'] == 'DAYS')
							{
								@$fields_remaining[$benefit_type."#".str_replace(" ","_",$value)."#".str_replace(" ","_",$dvalue['details'])] = '0';
							}

							//REMAINING FOR AMOUNT
							if(@$remainingDetails[$key][0]['remaining_amount'] != 0.00 && $dvalue['details'] == 'AMOUNT')
							{
								@$fields_remaining[$benefit_type."#".str_replace(" ","_",$value)."#".str_replace(" ","_",$dvalue['details'])] = $remainingDetails[$key][0]['remaining_amount'];
							}
							elseif(@$remainingDetails[$key][0]['remaining_amount'] <= 0.00 && $dvalue['details'] == 'AMOUNT')
							{
								@$fields_remaining[$benefit_type."#".str_replace(" ","_",$value)."#".str_replace(" ","_",$dvalue['details'])] = '0.00';
							}

							//REMAINING MODALITIES
							if($remainingDetails[$key][0]['remaining_amount'] != 0.00 && $dvalue['details'] == 'BY MODALITIES')
							{
								@$fields_remaining[$benefit_type."#".str_replace(" ","_",$value)."#".str_replace(" ","_",$dvalue['details'])] = $remainingDetails[$key][0]['remaining_amount'];
							}
							elseif($remainingDetails[$key][0]['remaining_amount'] <= 0.00 && $dvalue['details'] == 'BY MODALITIES')
							{
								@$fields_remaining[$benefit_type."#".str_replace(" ","_",$value)."#".str_replace(" ","_",$dvalue['details'])] = '0.00';
							}

							//REMAINING FOR AS CHARGED
							if(@$remainingDetails[$key][0]['remaining_as-charged'] != 0.00 && $dvalue['details'] == 'AS CHARGED')
							{
								@$fields_remaining[$benefit_type."#".str_replace(" ","_",$value)."#".str_replace(" ","_",$dvalue['details'])] = $remainingDetails[$key][0]['remaining_as-charged'];
							}
							elseif(@$remainingDetails[$key][0]['remaining_as-charged'] <= 0.00 && $dvalue['details'] == 'AS CHARGED')
							{
								@$fields_remaining[$benefit_type."#".str_replace(" ","_",$value)."#".str_replace(" ","_",$dvalue['details'])] = '0.00';
							}
						}
					}
				}

				if(isset($_POST['benefit_limit_type']))
				{
					if($illness_exist)
					{
						$data['fields_remaining'] = $fields_remaining;
					}
					else
					{
						$data['fields_remaining'] = NULL;
					}
				}
				else
				{
					$data['fields_remaining'] = $fields_remaining;
				}
				// $data['fields_remaining'] = @$fields_remaining;
				$data['fieldValue'] = $fieldValue;
				$data['details'] = $details;
				// echo '<pre>';
				// var_dump($data);

				var_dump($data);
				$loadedViews = array(
									'verifications/verifications_ipop_fill_details_view' => $data
									);
				$this->load->template($loadedViews, $this->header_links);
			}
		}
		else
		{
			$this->form_validation->set_rules('benefit_name','Benefit Name','trim|required|xss_clean');

			if($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('result',validation_errors());
				redirect('operations','refresh');
			}
			else
			{
				$data = $_POST;
				$benefit = $this->verifications_model->getRecordById('benefits.benefitset_info',$_POST['benefit_set_id']);
				$tableName = str_replace(" ","_", $benefit[0]['benefit_set_name']);
				$data['benefit_set_name'] = $benefit[0]['benefit_set_name'];
				$data['plan_type'] = $benefit[0]['plan_type'];

				$benefit_name = str_replace(" ","_",$_POST['benefit_name']);
				$details = $this->verifications_model->getRecordByMultiField('benefits.basic_benefitss',
																		array(
																			'benefit_type' => $benefit_type,
																			'benefit_name' => $_POST['benefit_name']
																			));

				$tableDetails = $this->verifications_model->getRecordByField('benefits.'.$tableName,'patient_id',$_POST['patient_id']);
				$data['fields'] = $tableDetails[0];

				//OVERALL BENEFIT LIMIT
				$overall_mbl_exist = $this->verifications_model->getRecordByField('benefits_overall_mbl','patient_id',$_POST['patient_id']);
				if($overall_mbl_exist)
				{
					$data['fields']['maximum_benefit_limit'] = $overall_mbl_exist[0]['remaining_overall_mbl'];
				}

				// CHECK IF BENEFIT LIMIT TYPE is PER ILLNESS, and if ILLNESS already exist in a patient
				if(isset($_POST['benefit_limit_type']))
				{
					$illness_exist = $this->verifications_model->getRecordByMultiField('patient_illness',
																				array('patient_id'=>$data['patient_id'],
																					'illness'=>$data['illness']));
			
					$detailsOthers = $this->verifications_model->getRecordByMultiField('benefits_others',
																				array('patient_id'=>$_POST['patient_id'],
																					'benefit_name'=>$_POST['benefit_name'],
																					'date'=>$illness_exist[0]['date']));
					
					$detailsAsCharged = $this->verifications_model->getRecordByField('benefits_others_as_charged',
																				array('patient_id'=>$_POST['patient_id'],
																					'benefit_name'=>$_POST['benefit_name'],
																					'date'=>$illness_exist[0]['date']));
				}
				else
				{
					$detailsOthers = $this->verifications_model->getRecordsByTwoFieldsOrderByDesc('benefits_others','patient_id','benefit_name',$_POST['patient_id'],$_POST['benefit_name']);
					$detailsAsCharged = $this->verifications_model->getRecordsByTwoFieldsOrderByDesc('benefits_others_as_charged','patient_id','benefit_name',$_POST['patient_id'],$_POST['benefit_name']);
				}

				foreach($details as $key => $value)
				{
					$details[$key] = $value['details'];
					$fieldValue[$key] = $value['benefit_type']."#".str_replace(" ","_",$value['benefit_name'])."#".$value['details'];

					if($detailsOthers || $detailsAsCharged)
					{
						//REMAINING DAY EXISTS
						if($detailsOthers[0]['remaining_days'] != 0 && $value['details'] == 'DAYS')
						{
							@$fields_remaining[$benefit_type."#".str_replace(" ","_",$_POST['benefit_name'])."#".str_replace(" ","_",$value['details'])] = $detailsOthers[0]['remaining_days'];
						}
						elseif(@$detailsOthers[0]['remaining_days'] <= 0 && $value['details'] == 'DAYS')
						{
							@$fields_remaining[$benefit_type."#".str_replace(" ","_",$_POST['benefit_name'])."#".str_replace(" ","_",$value['details'])] = '0';
						}

						//REMAINING AMOUNT EXISTS
						if($detailsOthers[0]['remaining_amount'] != 0.00 && $value['details'] == 'AMOUNT')
						{
							@$fields_remaining[$benefit_type."#".str_replace(" ","_",$_POST['benefit_name'])."#".str_replace(" ","_",$value['details'])] = $detailsOthers[0]['remaining_amount'];
						}
						elseif(@$detailsOthers[0]['remaining_amount'] <= 0.00 && $value['details'] == 'AMOUNT')
						{
							@$fields_remaining[$benefit_type."#".str_replace(" ","_",$_POST['benefit_name'])."#".str_replace(" ","_",$value['details'])] = '0.00';
						}

						// REMAINING BY MODALITIES
						if($detailsOthers[0]['remaining_amount'] != 0.00 && $value['details'] == 'BY MODALITIES')
						{
							@$fields_remaining[$benefit_type."#".str_replace(" ","_",$_POST['benefit_name'])."#".str_replace(" ","_",$value['details'])] = $detailsOthers[0]['remaining_amount'];
						}
						elseif(@$detailsOthers[0]['remaining_amount'] <= 0.00 && $value['details'] == 'BY MODALITIES')
						{
							@$fields_remaining[$benefit_type."#".str_replace(" ","_",$_POST['benefit_name'])."#".str_replace(" ","_",$value['details'])] = '0.00';
						}

						//REMAINING AS CHARGED EXISTS
						if($detailsAsCharged[0]['remaining_mbl_balance'] != 0.00 && $value['details'] == 'AS CHARGED')
						{
							@$fields_remaining[$benefit_type."#".str_replace(" ","_",$_POST['benefit_name'])."#".str_replace(" ","_",$value['details'])] = $detailsAsCharged[0]['remaining_mbl_balance'];
						}
						elseif(@$detailsAsCharged[0]['remaining_mbl_balance'] <= 0.00 && $value['details'] == 'AS CHARGED')
						{
							@$fields_remaining[$benefit_type."#".str_replace(" ","_",$_POST['benefit_name'])."#".str_replace(" ","_",$value['details'])] = '0.00';
						}
					}
				}

				if(isset($_POST['benefit_limit_type']))
				{
					if($illness_exist)
					{
						$data['fields_remaining'] = $fields_remaining;
					}
					else
					{
						$data['fields_remaining'] = NULL;
					}
				}
				else
				{
					$data['fields_remaining'] = $fields_remaining;
				}

				$data['fieldValue'] = $fieldValue;
				$data['details'] = $details;
				// echo '<pre>';
				// var_dump($data);
				$loadedViews = array(
								'verifications/verifications_fill_details_view' => $data
								);
				$this->load->template($loadedViews, $this->header_links);
			}
		}
	}

	function register()
	{
		// FOR OVERALL BENEFIT LIMIT
		$maximum_benefit_limit = $_POST['maximum_benefit_limit'];
		$overall_amount = 0.0;

		unset($_POST['submit'],$_POST['maximum_benefit_limit'],$_POST['overall']);
		$insufficient_benefits = array();

		$this->form_validation->set_rules('hospital_name','Hospital Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('hospital_branch','Hospital Branch', 'trim|xss_clean');
		$this->form_validation->set_rules('physician','Physician','trim|xss_clean|required');
		// $this->form_validation->set_rules('chief_complaint', 'Chief Complaint', 'trim|required|xss_clean');
		$this->form_validation->set_rules('remarks', 'Remarks', 'trim|xss_clean');

		$this->form_validation->set_rules('patient_name', 'Patient Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('patient_id', 'Patient ID', 'trim|required|xss_clean');
		$this->form_validation->set_rules('compins_id', 'Company Insurance ID', 'trim|required|xss_clean');
		$this->form_validation->set_rules('company_name','Company Name','trim|required|xss_clean');
		$this->form_validation->set_rules('insurance_name','Insurance Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('company_code', 'Company Code', 'trim|xss_clean');
		$this->form_validation->set_rules('insurance_code', 'Insurance Code', 'trim|xss_clean');
		$this->form_validation->set_rules('principal_name', 'Principal Name', 'trim|xss_clean');
		$this->form_validation->set_rules('date_encoded', 'Date Encoded', 'trim|required|xss_clean');

		$this->form_validation->set_rules('availment_type', 'Availment Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('benefit_set_name', 'Benefit Set Name', 'trim|required|xss_clean');
		// $this->form_validation->set_rules('benefit_name', 'Benefit Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('benefit_set_id', 'Benefit Set ID','trim|required|xss_clean');

		// $benefitset_info = $this->verifications_model->getRecordById('benefits.benefitset_info', $_POST['benefit_set_id']);
		// if($benefitset_info['plan_type'] == 'HMO')
		// {

		// }
		// elseif($benefitset_info['plan_type'] == 'Plan Limit Type')
		// {

		// }

		if($_POST['availment_type'] == 'In-Patient' || $_POST['availment_type'] == 'In and Out Patient')
		{
			if($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('result', validation_errors());
				redirect('verifications');
				echo validation_errors();
			}
			else
			{
				// echo '<pre>';
				// var_dump($_POST);
				$data = $_POST;
				$datetime_connection = date('Y-m-d H:i:s'); // USE FOR THE CONNECTION OF AVAILMENTS AND BENEFIT DETAILS
				$user = $this->session->userdata('logged_in');
				$data['claims_dateofavailment'] = date('Y-m-d H:i:s');
				$data['user'] = $user['name'];
				$data['date'] = $datetime_connection;

				// LOA / AVAILMENT CODE
				if(!isset($data['code']))
				{
					$data['code'] = $this->code->setCode();
				}

				unset($data['benefit_set_id'], $data['benefit_set_name'],
					$data['compins_id'], $data['lab_value'],
					$data['availed_days'], $data['availed_amount'],
					$data['registered_days'], $data['registered_amount'],
					$data['lab_test'],$data['amount'], $data['details_days'],
					$data['availed_as-charged'],$data['registered_as-charged'],
					$data['benefit_name'], $data['details'],$data['diagnosis'],
					$data['illness'], $data['benefit_limit_type'],
					$data['ill_test'], $data['benefit_name'] // WILL BE USED FOR REFERENCE
					);

				//STORED IN benefits_in-out_patient table
				// $ipop['patient_id'] = $_POST['patient_id'];
				// $ipop['compins_id'] = $_POST['compins_id'];
				// $ipop['benefit_set_id'] = $_POST['benefit_set_id'];

				$register = $this->verifications_model->register('availments_test',$data);

				if(isset($_POST['benefit_limit_type']))
				{
					$illness = array(
								'patient_id' => $_POST['patient_id'],
								'patient_name' => $_POST['patient_name'],
								'loa_code' => $data['code'],
								'illness' => $_POST['illness'],
								'date' => $datetime_connection
								);
					$register_illness = $this->verifications_model->register('patient_illness',$illness);
				}

				if($register)
				{
					if($_POST['availment_type'] == 'In and Out Patient')
					{
						foreach($_POST['diagnosis'] as $key => $value)
						{
							$diagnosis = array(
									'diagnosis' => $value,
									'code' => $data['code'],
									'date' => $datetime_connection
									);
							$diagnosisRegister = $this->verifications_model->register('availments_diagnosis',$diagnosis);
						}
					}

					foreach($_POST['benefit_name'] as $key => $value)
					{
						//STORED IN benefits_in-out_patient table
						$ipop[$key]['patient_id'] = $_POST['patient_id'];
						$ipop[$key]['compins_id'] = $_POST['compins_id'];
						$ipop[$key]['benefit_set_id'] = $_POST['benefit_set_id'];

						// if($value == 'LABORATORY')
						if(strpos($value,'LABORATORY') !== FALSE)
						{
							$laboratory['benefit_name'] = $value;
							$laboratory['availment_id'] = $register;
							$laboratory['benefit_set_id'] = $_POST['benefit_set_id'];
							$laboratory['compins_id'] = $_POST['compins_id'];
							$laboratory['patient_id'] = $_POST['patient_id'];
							$laboratory['code'] = $data['code'];
							$laboratory['date'] = $datetime_connection;

							$labInfo = array(
										'lab_test','amount'
										);
							$total_amount = 0.0;

							foreach($_POST['lab_test'] as $lkey => $lvalue)
							{
								foreach($labInfo as $field)
								{
									$labs[$lkey][$field] = $_POST[$field][$lkey];
									$total_amount += @$labs[$lkey]['amount'];
								}
							}
							$remaining_balance = $_POST['lab_value'] - $total_amount;
							
							if($_POST['details'][$key] == 'AS CHARGED')
							{
								$laboratory['availed_as-charged'] = $total_amount;
								$laboratory['remaining_as-charged'] = $remaining_balance;
							}
							elseif($_POST['details'][$key] == 'AMOUNT' || $_POST['details'][$key] == 'BY MODALITIES')
							{
								$laboratory['availed_amount'] = $total_amount;
								$laboratory['remaining_amount'] = $remaining_balance;
							}
							$overall_amount += $total_amount;

							//LABORATORY BENEFIT LIMIT
							if(($_POST['details'][$key] == 'AMOUNT' && $laboratory['remaining_amount'] <= '0') ||
								($_POST['details'][$key] == 'AS CHARGED' && $laboratory['remaining_as-charged'] <= '0') || 
								($_POST['details'][$key] == 'BY MODALITIES' && $laboratory['remaining_amount'] <= '0'))
							{
								array_push($insufficient_benefits,$value);
								unset($laboratory);
							}
							else
							{
								$labBenefits = $this->verifications_model->register('benefits_in-out_patient', $laboratory);

								foreach($labs as $labkey => $labvalue)
								{
									$labs[$labkey]['availments_id'] = $register;
									$labs[$labkey]['code'] = $data['code'];
									$labs[$labkey]['date'] = $datetime_connection;
									$registerLabs = $this->verifications_model->register('lab_test_test',$labs[$labkey]);
								}
							}
						}
						else
						{
							// DATA FOR benefits_in-out_patient
							$ipop[$key]['availment_id'] = $register;
							$ipop[$key]['benefit_name'] = $value;
							$ipop[$key]['code'] = $data['code'];
							$ipop[$key]['date'] = $datetime_connection;

							$ipop[$key]['availed_days'] = 0;
							$ipop[$key]['remaining_days'] = 0;
							$ipop[$key]['availed_amount'] = 0;
							$ipop[$key]['remaining_amount'] = 0;
							$ipop[$key]['availed_as-charged'] = 0;
							$ipop[$key]['remaining_as-charged'] = 0;
							
							//SET CONDITION TO A NEW VARIABLE
							$daysIsSetAmount = isset($_POST['details_days'][$key]) && isset($_POST['availed_days'][$key]) && $_POST['details'][$key] == 'AMOUNT';
							$daysIsSetAmountPerDay = isset($_POST['details_days'][$key]) && isset($_POST['availed_days'][$key]) && $_POST['details'][$key] == 'AMOUNT PER DAY';
							$daysNotSetAmount = !isset($_POST['details_days'][$key]) && $_POST['details'][$key] == 'AMOUNT'
												|| (isset($_POST['details_days'][$key]) && !isset($_POST['availed_days'][$key]));
							$asChargedSet = $_POST['details'][$key] == 'AS CHARGED';
							$byModalities = $_POST['details'][$key] == 'BY MODALITIES';
							if($daysIsSetAmount)
							{
								if($_POST['availed_days'][$key] == '')
								{
									$availed_days[$key] = '1';
								}
								else
								{
									$availed_days[$key] = $_POST['availed_days'][$key];
								}
								$ipop[$key]['availed_days'] = $availed_days[$key];
								$ipop[$key]['remaining_days'] = $_POST['registered_days'][$key] - $availed_days[$key];
								// $ipop_availed_amount = $availed_days[$key] * $_POST['availed_amount'][$key];
								$ipop_availed_amount = $_POST['availed_amount'][$key];
								$ipop[$key]['availed_amount'] = $ipop_availed_amount;
								$ipop[$key]['remaining_amount'] = $_POST['registered_amount'][$key] - $ipop_availed_amount;

								$overall_amount += $ipop_availed_amount;
							}

							if($daysIsSetAmountPerDay)
							{
								if($_POST['availed_days'][$key] == '')
								{
									$availed_days[$key] = '1';
								}
								else
								{
									$availed_days[$key] = $_POST['availed_days'][$key];
								}
								$ipop[$key]['availed_days'] = $availed_days[$key];
								$ipop[$key]['remaining_days'] = $_POST['registered_days'][$key] - $availed_days[$key];
								$ipop_availed_amount = $availed_days[$key] * $_POST['registered_amount'][$key];
								$ipop[$key]['availed_amount'] = $ipop_availed_amount;
								$ipop[$key]['remaining_amount'] = $_POST['registered_amount'][$key];

								$overall_amount += $ipop_availed_amount;
							}

							if($daysNotSetAmount)
							{
								$ipop[$key]['availed_amount'] = $_POST['availed_amount'][$key];
								$ipop[$key]['remaining_amount'] = $_POST['registered_amount'][$key] - $_POST['availed_amount'][$key];
								$overall_amount += $_POST['availed_amount'][$key];
							}

							if($byModalities)
							{
								$ipop[$key]['availed_amount'] = $_POST['availed_amount'][$key];
								$ipop[$key]['remaining_amount'] = $_POST['registered_amount'][$key] - $_POST['availed_amount'][$key];
								$overall_amount += $_POST['availed_amount'][$key];
							}

							if($asChargedSet)
							{
								$ipop[$key]['availed_as-charged'] = $_POST['availed_as-charged'][$key];
								$ipop[$key]['remaining_as-charged'] = $_POST['registered_as-charged'][$key] - $_POST['availed_as-charged'][$key];
								$overall_amount += $_POST['availed_as-charged'][$key];
							}

							//LIMIT FOR EACH BENEFITS
							if(($daysIsSetAmount && $ipop[$key]['remaining_days'] < '0' || $daysIsSetAmount && $ipop[$key]['remaining_amount'] < '0')
							|| ($daysIsSetAmountPerDay && $ipop[$key]['remaining_days'] < '0')
							|| ($daysNotSetAmount && $ipop[$key]['remaining_amount'] < '0') || $asChargedSet && $ipop[$key]['remaining_as-charged'] < '0'
							|| (($daysIsSetAmount && $ipop[$key]['availed_amount'] <= '0.00') || $daysNotSetAmount && $ipop[$key]['availed_amount'] <= '0.00')
							|| ($asChargedSet && $ipop[$key]['availed_as-charged'] <= '0.00')
							|| in_array(NULL, $ipop[$key], TRUE))
							{
								array_push($insufficient_benefits,$value);
								unset($ipop[$key]);
							}
							else
							{
								$registerIPOP = $this->verifications_model->register('benefits_in-out_patient',$ipop[$key]);
							}
						}
					}

					//FOR OVERALL BENEFIT LIMIT
					$overall_mbl_exist = $this->verifications_model->getRecordByMultiField('benefits_overall_mbl',array('patient_id'=>$_POST['patient_id']));
					if($overall_mbl_exist)
					{
						$mbl = $overall_mbl_exist[0];
						$remaining_mbl = $mbl['remaining_overall_mbl'] - $overall_amount;

						if($remaining_mbl <= 0)
						{
							echo '<script>alert("Failed to register Verifications. This patient already reached his/her Maximum Benefit Limit!!!");</script>';
							$delete1 = $this->verifications_model->delete('availments_test','code',$data['code']);
							$delete2 = $this->verifications_model->delete('patient_illness','loa_code',$data['code']);
							$delete3 = $this->verifications_model->delete('availments_diagnosis','code',$data['code']);
							$delete4 = $this->verifications_model->delete('lab_test_test','code',$data['code']);
							$delete5 = $this->verifications_model->delete('benefits_in-out_patient','code',$data['code']);
							$this->session->set_flashdata('result','Failed to register Verifications. This patient already reached his/her Maximum Benefit Limit!');
							redirect('operations','refresh');
						}
						else
						{
							$data = array(
								'code' => $data['code'],
								'remaining_overall_mbl' => $remaining_mbl,
								'date' => $datetime_connection
								);
							$patient_mbl = $this->verifications_model->update('benefits_overall_mbl','patient_id',$_POST['patient_id'],$data);
						}
					}
					else
					{
						$remaining_mbl = $maximum_benefit_limit - $overall_amount;

						if($remaining_mbl <= 0)
						{
							echo '<script>alert("Failed to register Verifications. This patient already reached his/her Maximum Benefit Limit!!!");</script>';
							$delete1 = $this->verifications_model->delete('availments_test','code',$data['code']);
							$delete2 = $this->verifications_model->delete('patient_illness','loa_code',$data['code']);
							$delete3 = $this->verifications_model->delete('availments_diagnosis','code',$data['code']);
							$delete4 = $this->verifications_model->delete('lab_test_test','code',$data['code']);
							$delete5 = $this->verifications_model->delete('benefits_in-out_patient','code',$data['code']);
							$this->session->set_flashdata('result','Failed to register Verifications. This patient already reached his/her Maximum Benefit Limit!');
							redirect('operations','refresh');
						}
						else
						{
							$data = array(
								'patient_id' => $_POST['patient_id'],
								'code' => $data['code'],
								'remaining_overall_mbl' => $remaining_mbl,
								'date' => $datetime_connection
								);
							$patient_mbl = $this->verifications_model->register('benefits_overall_mbl',$data);
						}
					}

					if($registerIPOP || $registerLabs || $patient_mbl)
					{
						// echo "<pre>";
						// var_dump($_POST);
						// var_dump($registerIPOP);
						// var_dump($registerLabs);
						$this->session->set_flashdata('insufficient_benefits',$insufficient_benefits);
						$this->session->set_flashdata('result','Successfully registered LOA.');
						redirect('verifications');
					}
					else
					{
						$delete = $this->verifications_model->delete('availments_test','id', $register);
						$this->session->set_flashdata('result','LOA registration FAILED. Something went wrong.');
						redirect('verifications');
					}
				}
			}
		}
		else
		{
			if($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('result', validation_errors());
				redirect('verifications');
				echo validation_errors();
			}
			else // LOA REGISTRATION FOR Out-Patient
			{
				switch($_POST['details'])
				{
					case 'DAYS':
					case 'AMOUNT':
					case 'AMOUNT PER DAY':
						// unset($_POST['details']);
						$data = $_POST;
						unset($data['details']);
						$user = $this->session->userdata('logged_in');
						$datetime_connection = date('Y-m-d H:i:s');
						$data['claims_dateofavailment'] = date('Y-m-d H:i:s');
						$data['user'] = $user['name'];
						$data['date'] = $datetime_connection;

						// LOA / AVAILMENT CODE
						$data['code'] = $this->code->setCode();

						unset($data['benefit_set_id'],$data['benefit_set_name'],
							$data['compins_id'],$data['lab_value'],
							$data['availed_days'],$data['availed_amount'],
							$data['registered_days'],$data['registered_amount'],
							$data['diagnosis'],$data['illness'],
							$data['benefit_limit_type']
							);

						//OTHER BENEFITS
						$others['benefit_name'] = $_POST['benefit_name'];
						$others['patient_id'] = $_POST['patient_id'];
						$others['compins_id'] = $_POST['compins_id'];
						$others['benefit_set_id'] = $_POST['benefit_set_id'];
						$others['availed_days'] = $_POST['availed_days'];
						$others['remaining_days'] = $_POST['registered_days'] - $_POST['availed_days'];
						$others['code'] = $data['code'];
						$others['date'] = $datetime_connection;

						if(isset($_POST['availed_days']) == TRUE)
						{
							if($_POST['details'] == 'AMOUNT')
							{
								$availed_amount = $_POST['availed_amount'];
								$others['availed_amount'] = $availed_amount;
								$others['remaining_amount'] = $_POST['registered_amount'] - $availed_amount;
							}
							if($_POST['details'] == 'AMOUNT PER DAY')
							{
								$others['availed_amount'] = $_POST['registered_amount'] * $_POST['availed_days'];
								$others['remaining_amount'] = $_POST['registered_amount'];
							}
							// $availed_amount = $_POST['availed_days'] * $_POST['availed_amount'];
							// $others['availed_amount'] = $availed_amount;
							// $others['remaining_amount'] = $_POST['registered_amount'] - $availed_amount;
						}
						else
						{
							$others['availed_amount'] = $_POST['availed_amount'];
							$others['remaining_amount'] = $_POST['registered_amount'] - $_POST['availed_amount'];
							$others['remaining_days'] = 1;
						}
						$register = $this->verifications_model->register('availments_test',$data);

						// LABORATORY FOR OUT-PATIENT OTHERS BENEFITS
						if(strpos($others['benefit_name'],'LABORATORY') !== FALSE)
						{
							$labInfo = array('lab_test','amount');
							$total_amount = 0.0;

							foreach($_POST['lab_test'] as $lkey => $lvalue)
							{
								foreach($labInfo as $field)
								{
									$labs[$lkey][$field] = $_POST[$field][$lkey];
									$total_amount += @$labs[$lkey]['amount'];
								}
							}
							if(isset($_POST['lab_value']))
							{
								$remaining_balance = $_POST['lab_value'] - $total_amount;
								$others['availed_amount'] = $total_amount;
								$others['remaining_amount'] = $remaining_balance;
							}
							
							foreach($labs as $labkey => $labvalue)
							{
								$labs[$labkey]['availments_id'] = $register;
								$labs[$labkey]['code'] = $data['code'];
								$labs[$labkey]['date'] = $datetime_connection;
								$registerLabs = $this->verifications_model->register('lab_test_test',$labs[$labkey]);
							}
						}

						if(isset($_POST['benefit_limit_type']))
						{
							$illness = array(
										'patient_id' => $_POST['patient_id'],
										'patient_name' => $_POST['patient_name'],
										'loa_code' => $data['code'],
										'illness' => $_POST['illness'],
										'date' => $datetime_connection
										);
							$register_illness = $this->verifications_model->register('patient_illness',$illness);
						}

						// echo '<pre>';
						// var_dump($_POST);
						// var_dump(isset($_POST['availed_days']));
						// var_dump($others);
						//LIMIT BENEFITS BASED ON REMAINING AMOUNT AND REMAINING DAYS
						if($others['remaining_amount'] <= '0' || $others['remaining_days'] <= '0')
						{
							$delete = $this->verifications_model->delete('availments_test','id',$register);
							$this->session->set_flashdata('result','You have reach the limit for '.$others['benefit_name'].' Benefit.');
							// var_dump($others);
							redirect('operations','result');
						}
						else
						{
							if($register)
							{
								$others['availment_id'] = $register;
								foreach($_POST['diagnosis'] as $key => $value)
								{
									if($value == '')
									{
										unset($value);
									}
									else
									{
										$diagnosis = array(
												'diagnosis' => $value,
												'code' => $data['code'],
												'date' => $datetime_connection
												);
										$diagnosisRegister = $this->verifications_model->register('availments_diagnosis',$diagnosis);
									}	
								}

								$others['availment_id'] = $register;
								$registerOthers = $this->verifications_model->register('benefits_others',$others);

								if($registerOthers)
								{
									$this->session->set_flashdata('result','Successfully registered LOA.');
									redirect('verifications');
								}
								else
								{
									$this->form_validation->set_message('result','Something is wrong.');
									return FALSE;
								}
							}
							else
							{
								$this->form_validation->set_message('result','Failed to register LOA.');
								return FALSE;
							}
						}
					break;

					case 'AS CHARGED':
						unset($_POST['details']);
						$data = $_POST;
						$user = $this->session->userdata('logged_in');
						$datetime_connection = date('Y-m-d H:i:s');
						$data['claims_dateofavailment'] = date('Y-m-d H:i:s');
						$data['user'] = $user['name'];
						$data['date'] = $datetime_connection;

						// LOA / AVAILMENT CODE
						$data['code'] = $this->code->setCode();

						unset($data['lab_value'], $data['compins_id'],
							$data['benefit_set_id'], $data['benefit_set_name'],
							$data['availed_amount'], $data['registered_amount'],
							$data['diagnosis'],$data['illness'],$data['benefit_limit_type'],
							$data['lab_test'],$data['amount']
							);

						//AS CHARGED
						$as_charged['benefit_name'] = $_POST['benefit_name'];
						$as_charged['patient_id'] = $_POST['patient_id'];
						$as_charged['compins_id'] = $_POST['compins_id'];
						$as_charged['benefit_set_id'] = $_POST['benefit_set_id'];
						$as_charged['availed_amount'] = $_POST['availed_amount'];
						$as_charged['remaining_mbl_balance'] = $_POST['registered_amount'] - $_POST['availed_amount'];
						$as_charged['code'] = $data['code'];
						$as_charged['date'] = $datetime_connection;

						$overall_amount += $as_charged['availed_amount'];

						$register = $this->verifications_model->register('availments_test',$data);

						// LABORATORY FOR OUT-PATIENT AS CHARGED
						if($register)
						{
							if(strpos($as_charged['benefit_name'], 'LABORATORY') !== FALSE)
							{
								// var_dump(strpos($as_charged['benefit_name'],'LABORATORY'));
								// // var_dump($_POST);
								// $test = strpos($as_charged['benefit_name'],'LABORATORY');
								// var_dump($test);
								$labInfo = array(
										'lab_test', 'amount'
										);
								$total_amount = 0.0;

								foreach($_POST['lab_test'] as $lkey => $lvalue)
								{
									foreach($labInfo as $field)
									{
										$labs[$lkey][$field] = $_POST[$field][$lkey];
										$total_amount += @$labs[$lkey]['amount'];
									}
								}

								if(isset($_POST['lab_value']))
								{
									$remaining_balance = $_POST['lab_value'] - $total_amount;
									$as_charged['availed_amount'] = $total_amount;
									$as_charged['remaining_mbl_balance'] = $remaining_balance;
								}
								$overall_amount += $total_amount;

								foreach($labs as $labkey => $labvalue)
								{
									// echo '<pre>';
									// var_dump($labs);
									$labs[$labkey]['availments_id'] = $register;
									$labs[$labkey]['code'] = $data['code'];
									$labs[$labkey]['date'] = $datetime_connection;
									// var_dump($labs);
									$registerLabs = $this->verifications_model->register('lab_test_test',$labs[$labkey]);
									// var_dump($registerLabs);
								}
							}
						}

						if(isset($_POST['benefit_limit_type']))
						{
							$illness = array(
										'patient_id' => $_POST['patient_id'],
										'patient_name' => $_POST['patient_name'],
										'loa_code' => $data['code'],
										'illness' => $_POST['illness'],
										'date' => $datetime_connection
										);
							$register_illness = $this->verifications_model->register('patient_illness',$illness);
						}

						if($as_charged['remaining_mbl_balance'] <= '0')
						{
							$delete = $this->verifications_model->delete('availments_test','id',$register);
							$this->session->set_flashdata('result','You have entered/reach the limit for <b>'.$as_charged['benefit_name'].'</b> Benefit.');
							redirect('operations','refresh');
						}
						else
						{
							if($register)
							{
								$as_charged['availment_id'] = $register;

								foreach($_POST['diagnosis'] as $key => $value)
								{
									if($value == '')
									{
										unset($value);
									}
									else
									{
										$diagnosis = array(
												'diagnosis' => $value,
												'code' => $data['code'],
												'date' => $datetime_connection
												);
										$diagnosisRegister = $this->verifications_model->register('availments_diagnosis',$diagnosis);
									}	
								}

								$registerAsCharged = $this->verifications_model->register('benefits_others_as_charged',$as_charged);

								//FOR OVERALL BENEFIT LIMIT
								$overall_mbl_exist = $this->verifications_model->getRecordByMultiField('benefits_overall_mbl',array('patient_id'=>$_POST['patient_id']));
								if($overall_mbl_exist)
								{
									$mbl = $overall_mbl_exist[0];
									$remaining_mbl = $mbl['remaining_overall_mbl'] - $overall_amount;

									if($remaining_mbl <= 0)
									{
										echo '<script>alert("Failed to register Verifications. This patient already reached his/her Maximum Benefit Limit!!!");</script>';
										$delete1 = $this->verifications_model->delete('availments_test','code',$data['code']);
										$delete2 = $this->verifications_model->delete('patient_illness','loa_code',$data['code']);
										$delete3 = $this->verifications_model->delete('availments_diagnosis','code',$data['code']);
										$delete4 = $this->verifications_model->delete('lab_test_test','code',$data['code']);
										$delete5 = $this->verifications_model->delete('benefits_others_as_charged','code',$data['code']);
										$this->session->set_flashdata('result','Failed to register Verifications. This patient already reached his/her Maximum Benefit Limit!');
										redirect('operations','refresh');
									}
									else
									{
										$data = array(
											'code' => $data['code'],
											'remaining_overall_mbl' => $remaining_mbl,
											'date' => $datetime_connection
											);
										$patient_mbl = $this->verifications_model->update('benefits_overall_mbl','patient_id',$_POST['patient_id'],$data);
									}
								}
								else
								{
									$remaining_mbl = $maximum_benefit_limit - $overall_amount;

									if($remaining_mbl <= 0)
									{
										echo '<script>alert("Failed to register Verifications. This patient already reached his/her Maximum Benefit Limit!!!");</script>';
										$delete1 = $this->verifications_model->delete('availments_test','code',$data['code']);
										$delete2 = $this->verifications_model->delete('patient_illness','loa_code',$data['code']);
										$delete3 = $this->verifications_model->delete('availments_diagnosis','code',$data['code']);
										$delete4 = $this->verifications_model->delete('lab_test_test','code',$data['code']);
										$delete5 = $this->verifications_model->delete('benefits_others_as_charged','code',$data['code']);
										$this->session->set_flashdata('result','Failed to register Verifications. This patient already reached his/her Maximum Benefit Limit!');
										redirect('operations','refresh');
									}
									else
									{
										$data = array(
											'patient_id' => $_POST['patient_id'],
											'code' => $data['code'],
											'remaining_overall_mbl' => $remaining_mbl,
											'date' => $datetime_connection
											);
										$patient_mbl = $this->verifications_model->register('benefits_overall_mbl',$data);
									}
								}

								if($registerAsCharged || $patient_mbl)
								{
									$this->session->set_flashdata('result','Successfully registered LOA.');
									redirect('verifications');
								}
								else
								{
									$this->form_validation->set_message('result','Something is wrong.');
									return FALSE;
								}
							}
							else
							{
								$this->form_validation->set_message('result','FAILED to register LOA.');
								return FALSE;
							}
						}					
					break;
					default:
						echo '<pre>';
						var_dump($_POST);
				}
			}
		}		
	}

	function saveAdmission()
	{
		// var_dump($_POST);
		unset($_POST['file']);
		$this->form_validation->set_rules('patient_id','Patient ID','trim|required|xss_clean');
		$this->form_validation->set_rules('compins_id','Company ID','trim|required|xss_clean');
		$this->form_validation->set_rules('benefit_set_id','Benefit Set ID','trim|required|xss_clean');
		$this->form_validation->set_rules('hospital_name','Hospital Name','trim|required|xss_clean');
		$this->form_validation->set_rules('hospital_branch','Hospital Branch','trim|xss_clean');
		// $this->form_validation->set_rules('chief_complaint','Chief Complaint','trim|required|xss_clean');
		$this->form_validation->set_rules('date_admitted','Date Admitted','trim|required|xss_clean');
		$this->form_validation->set_rules('patient_name','Patient Name', 'trim|required|xss_clean');

		$this->form_validation->set_rules('history','History','trim|xss_clean');
		$this->form_validation->set_rules('remarks','Remarks','trim|xss_clean');
		$this->form_validation->set_rules('timefrom','Time From','trim|xss_clean');
		$this->form_validation->set_rules('timeto','Time To','trim|xss_clean');
		$this->form_validation->set_rules('dateofemail','Date Of Email','trim|xss_clean');
		$this->form_validation->set_rules('approved','Approved By','trim|xss_clean');
		$this->form_validation->set_rules('declined','Declined By','trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('verifications','refresh');
		}
		else
		{
			$data = $_POST;
			unset($data['file'], $data['specialist_name'],$data['specialist_fee'],$data['availment_type'],
				$data['benefit_name'], $data['principal_name'], $data['date_encoded'],
				$data['submit'],$data['diagnosis'],$data['illness'],$data['benefit_limit_type']);

			// VALIDATE DISCHARGED STATUS
			if($_POST['submit'] == 'Discharge Patient')
			{
				$data['discharge_status'] = 'Discharged';
			}
			else
			{
				$data['discharge_status'] = 'Running';
			}

			$user = $this->session->userdata('logged_in');
			$data['user'] = $user['name'];
			$data['code'] = $this->code->setCode();
			// $specialist_name = array_values(array_unique($_POST['specialist_name']));

			$save_admission_report = $this->verifications_model->register('admission_report_test',$data);

			$saved = $this->verifications_model->getRecordById('admission_report_test',$save_admission_report);

			if($save_admission_report)
			{
				foreach($_POST['diagnosis'] as $key => $value)
				{
					$diagnosis = array(
								'diagnosis' => $value,
								'code' => $data['code']
								);
					$diagnosisRegister = $this->verifications_model->register('availments_diagnosis',$diagnosis);
				}

				// foreach(array_unique($specialist_name) as $key => $value)
				foreach($_POST['specialist_name'] as $key => $value)
				{
					if($value == '')
					{
						unset($value);
					}
					else
					{
						$data_specialist = array('code' => $data['code'],
												'specialist_name' => $value,
												'specialist_fee' => $_POST['specialist_fee'][$key],
												'time_generated' => $saved[0]['time_generated']
												);
						$save_specialist = $this->verifications_model->register('admission_specialist', $data_specialist);
					}
				}
			}

			$data['availment_type'] = $_POST['availment_type'];
			$data['principal_name'] = $_POST['principal_name'];
			$data['date_encoded'] = $_POST['date_encoded'];
			$data['benefit_name'] = $_POST['benefit_name'];
			$data['diagnosis'] = $_POST['diagnosis'];
			$data['illness'] = $_POST['illness'];
			$data['benefit_limit_type'] = $_POST['benefit_limit_type'];
			unset($data['submit'], $data['discharge_status']);

			if($save_admission_report || $save_specialist)
			{
				if($_POST['submit'] == 'Discharge Patient')
				{
					$table = str_replace(" ","_",$_POST['benefit_name']);
					$key1 = $_POST['benefit_set_id'];
					$key2 = $_POST['availment_type'];
					$benefit_type = 'IP';

					$benefit_details = $this->verifications_model->getRecordByMultiField('benefits.benefitset_details',
																					array(
																						'benefit_set_id' => $key1,
																						'benefit_type' => $key2
																						));

					foreach($benefit_details as $key => $value)
					{
						$details[$value['registered_benefit']] = $this->verifications_model->getRecordByMultiField('benefits.basic_benefitss',
																											array(
																												'benefit_type' => $benefit_type,
																												'benefit_name' => $value['registered_benefit']
																												));
						$benefit_name[$value['registered_benefit']] = $value['registered_benefit'];
					}
					$data['options'] = $benefit_name;
					// echo '<pre>';
					// var_dump($data);
					$this->session->set_flashdata('result','<b>Successfully Generate Admission Report');
					$loadedViews = array(
									'verifications/verifications_register_LOA_view' => $data
									);
					$this->load->template($loadedViews, $this->header_links);
				}
				else
				{
					$this->session->set_flashdata('result','<b>Successfully generated Admission Report.<b>');
					$loadedViews = array(
								'verifications/verifications_monitoring_view' => $data
								);
					$this->load->template($loadedViews, $this->header_links);
				}
			}
			else
			{
				$this->session->set_flashdata('result','<b>Something went wrong.</b>');
				redirect('operations','refresh');
			}
		}
	}

	function generateMonitoring()
	{
		// var_dump($_POST);
		$this->form_validation->set_rules('patient_id','Patient ID','required|trim|xss_clean');
		$this->form_validation->set_rules('compins_id', 'Company - Insurance ID', 'required|trim|xss_clean');
		$this->form_validation->set_rules('benefit_set_id','Benefit Set ID','required|trim|xss_clean');
		$this->form_validation->set_rules('patient_name','Patient Name','required|trim|xss_clean');
		$this->form_validation->set_rules('company_name','Company Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('insurance_name','Insurance Name','required|trim|xss_clean');
		$this->form_validation->set_rules('hospital_name','Hospital Name','required|trim|xss_clean');
		$this->form_validation->set_rules('hospital_branch','Hospital Branch','trim|xss_clean');
		// $this->form_validation->set_rules('chief_complaint','Chief Complaint','required|trim|xss_clean');

		$this->form_validation->set_rules('date','Date','trim|xss_clean');
		$this->form_validation->set_rules('monitoring_time','Time','trim|xss_clean');
		$this->form_validation->set_rules('running_bill','Running Bill', 'trim|xss_clean');
		$this->form_validation->set_rules('caller_name','Caller','trim|xss_clean');
		$this->form_validation->set_rules('status','Status', '|trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('operations','refresh');
		}
		else
		{
			$data = $_POST;
			unset($data['submit'],$data['availment_type'],$data['principal_name'],
				$data['benefit_name'], $data['date_encoded'],$data['diagnosis'],
				$data['illness'],$data['benefit_limit_type']);

			if($_POST['submit'] == 'Discharge Patient')
			{
				$data['discharge_status'] = 'Discharged';

				$admission = $this->verifications_model->getRecordByField('admission_report_test','code',$data['code']);

				if($admission)
				{
					foreach($admission as $key => $value)
					{
						$discharge = array(
								'discharge_status' => 'Discharged'
									);
						$update_admission = $this->verifications_model->update('admission_report_test','code',$data['code'],$discharge);
					}
				}
			}
			else
			{
				$data['discharge_status'] = 'Running';
			}

			$monitoring = $this->verifications_model->register('monitoring',$data);

			unset($data['date'], $data['monitoring_time'], $data['running_bill'],
				$data['caller_name'], $data['status'], $data['history']);

			if($monitoring)
			{
				if($_POST['submit'] == 'Save Monitoring Report')
				{
					$this->session->set_flashdata('result','<b>Successfully Generated Monitoring Report.</b>');
					redirect('operations','refresh');
				}
				else
				{
					$data['availment_type'] = $_POST['availment_type'];
					$data['benefit_set_id'] = $_POST['benefit_set_id'];
					$data['benefit_name'] = $_POST['benefit_name'];
					$data['company_code'] = $_POST['company_code'];
					$data['insurance_code'] = $_POST['insurance_code'];
					$data['date_encoded'] = $_POST['date_encoded'];
					$data['code'] = $_POST['code'];

					//BENEFIT INFORMATION
					$table = str_replace(" ","_",$_POST['benefit_name']);
					$key1 = $_POST['benefit_set_id'];
					$key2 = $_POST['availment_type'];
					$benefit_type = 'IP';

					$benefit_details = $this->verifications_model->getRecordByMultiField('benefits.benefitset_details',
																					array(
																						'benefit_set_id' => $key1,
																						'benefit_type' => $key2
																						));
					foreach($benefit_details as $key => $value)
					{
						$details[$value['registered_benefit']] = $this->verifications_model->getRecordByMultiField('benefits.basic_benefitss',
																												array(
																													'benefit_type' => $benefit_type,
																													'benefit_name' => $value['registered_benefit']
																													));
						$benefit_name[$value['registered_benefit']] = $value['registered_benefit'];
					}
					$data['options'] = $benefit_name;
					$data['diagnosis'] = $_POST['diagnosis'];
					$data['illness'] = $_POST['illness'];
					$data['benefit_limit_type'] = $_POST['benefit_limit_type'];

					$this->session->set_flashdata('result','<b>Successfully Registered Admission and Monitoring Report');

					$loadedViews = array(
								'verifications/verifications_register_LOA_view' => $data
								);
					$this->load->template($loadedViews, $this->header_links);
				}
			}	
			else
			{
				$this->session->set_flashdata('result','<b>Something went wrong.</b>');
				redirect('operations','refresh');
			}
		}
	}

	function editAdmission($code,$time_generated)
	{
		$time_generated = rawurldecode($time_generated);

		$admission = $this->verifications_model->getRecordByMultiField('admission_report_test',
																	array(
																		'code' => $code,
																		'time_generated' => $time_generated
																		));
		$specialist = $this->verifications_model->getRecordByMultiField('admission_specialist',
																	array(
																		'code' => $code,
																		'time_generated' => $time_generated
																		));
		$diagnosis = $this->verifications_model->getRecordByField('availments_diagnosis','code',$code);

		foreach($diagnosis as $key => $value)
		{
			$diagnosis_name[] = $value['diagnosis'];
		}
		$admission[0]['diagnosis'] = $diagnosis_name;

		if(@$specialist)
		{
			foreach($specialist as $key => $value)
			{
				$specialist_name[] = $value['specialist_name'];
			}
			$admission[0]['specialist_name'] = $specialist_name;
		}

		$doctors = $this->verifications_model->getDoctors('dentistsanddoctors');
		$empty = array(''=>'');

		if($doctors)
		{
			foreach($doctors as $key => $value)
			{
				$specialists[$value['type'].'. '.$value['firstname'].' '.$value['middlename'].' '.$value['lastname']] = $value['type'].'. '.$value['firstname'].' '.$value['middlename'].' '.$value['lastname'];
			}
			$admission[0]['specialist'] = array_merge($empty,$specialists);
		}

		foreach($admission as $row)
		{
			// var_dump($row['diagnosis']);
			$loadedViews = array(
							'verifications/verifications_admission_update_view' => $row
							);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function editMonitoring($code,$time_generated)
	{
		$time_generated = rawurldecode($time_generated);
		$monitoring = $this->verifications_model->getRecordByMultiField('monitoring',
																	array(
																		'code' => $code,
																		'time_generated' => $time_generated
																		));
		$diagnosis = $this->verifications_model->getRecordByField('availments_diagnosis','code',$code);

		foreach($diagnosis as $key => $value)
		{
			$diagnosis_name[] = $value['diagnosis'];
		}
		$monitoring[0]['diagnosis'] = $diagnosis_name;

		if($monitoring)
		{
			foreach($monitoring as $row)
			{
				// var_dump($row);
				$loadedViews = array(
								'verifications/verifications_monitoring_update_view' => $row
								);
				$this->load->template($loadedViews,$this->header_links);
			}
		}
	}

	function updateAdmission()
	{
		$data = $_POST;
		unset($data['file'],$data['specialist_name'],$data['availment_type'],
			$data['benefit_name'],$data['principal_name'],$data['date_encoded'],
			$data['submit'],$data['diagnosis']);

		$user = $this->session->userdata('logged_in');
		$data['user'] = $user['name'];

		if($_POST['submit'] == 'Discharge Patient')
		{
			$data['discharge_status'] = 'Discharged';
			
			//IF monitoring and admission exists AND discharge_status == DISCHARGED, monitoring and admission will be then DISCHARGED
			$admission = $this->verifications_model->getRecordByField('admission_report_test','code',$data['code']);
			$monitoring = $this->verifications_model->getRecordByField('monitoring','code',$data['code']);
			
			if($admission)
			{
				foreach($admission as $key => $value)
				{
					$discharge = array(
								'discharge_status' => 'Discharged'
									);
					$update_admission = $this->verifications_model->update('admission_report_test','code',$data['code'],$discharge);
				}
			}

			if($monitoring)
			{
				foreach($monitoring as $key => $value)
				{
					$discharge = array(
								'discharge_status' => 'Discharged'
								);
					$update_monitoring = $this->verifications_model->update('monitoring','code',$data['code'],$discharge);
				}
			}
		}
		else
		{
			$data['discharge_status'] = 'Running';
		}

		$specialist_name = array_values(array_unique($_POST['specialist_name']));

		$save_admission_report = $this->verifications_model->register('admission_report_test',$data);

		$updated = $this->verifications_model->getRecordById('admission_report_test',$save_admission_report);

		if($save_admission_report)
		{
			foreach(array_unique($specialist_name) as $key => $value)
			{
				if($value == '')
				{
					unset($value);
				}
				else
				{
					$data_specialist = array('code' => $data['code'],
											'specialist_name' => $value,
											'time_generated' => $updated[0]['time_generated']
											);
					$save_specialist = $this->verifications_model->register('admission_specialist',$data_specialist);
				}
			}
		}

		if($save_admission_report || $save_specialist)
		{
			if($_POST['submit'] == 'Discharge Patient')
			{
				$benefit_info = $this->verifications_model->getRecordById('benefits.benefitset_info',$data['benefit_set_id']);
				$benefit_set_name = $benefit_info[0]['benefit_set_name'];
				$table = str_replace(" ","_",$benefit_set_name);
				$key1 = $_POST['benefit_set_id'];
				$key2 = 'In-Patient';
				$benefit_type = 'IP';
				$data['availment_type'] = $key2;
				$data['benefit_name'] = $benefit_info[0]['benefit_set_name'];

				$benefit_details = $this->verifications_model->getRecordByMultiField('benefits.benefitset_details',
																				array(
																					'benefit_set_id' => $key1,
																					'benefit_type' => $key2
																					));
				foreach($benefit_details as $key => $value)
				{
					$details[$value['registered_benefit']] = $this->verifications_model->getRecordByMultiField('benefits.basic_benefitss',
																											array(
																												'benefit_type' => $benefit_type,
																												'benefit_name' => $value['registered_benefit']
																												));
					$benefit_name[$value['registered_benefit']] = $value['registered_benefit'];
				}

				$diagnosis = $this->verifications_model->getRecordByField('availments_diagnosis','code',$_POST['code']);
				foreach($diagnosis as $key => $value)
				{
					$diagnosis_name[] = $value['diagnosis'];
				}
				$data['diagnosis'] = $diagnosis_name;

				$data['options'] = $benefit_name;

				$this->session->set_flashdata('result','<b>Successfully Discharge Admission Report');
				$loadedViews = array(
								'verifications/verifications_register_LOA_view' => $data
								);
				$this->load->template($loadedViews, $this->header_links);
			}
			else
			{
				$this->session->set_flashdata('result','<b>Successfully Updated Admission Report.</b>');
				redirect('operations','refresh');
			}
		}
		else
		{
			$this->session->set_flashdata('result','<b>Something went wrong.</b>');
			redirect('operations','refresh');
		}
	}

	function updateMonitoring()
	{
		// var_dump($_POST);
		$data = $_POST;
		unset($data['submit'],$data['diagnosis']);

		if($_POST['submit'] == 'Discharge Patient')
		{
			$data['discharge_status'] = 'Discharged';

			//IF monitoring and admission exists AND discharge_status == DISCHARGED, monitoring and admission will be then DISCHARGED
			$admission = $this->verifications_model->getRecordByField('admission_report_test','code',$data['code']);
			$monitoring = $this->verifications_model->getRecordByField('monitoring','code',$data['code']);
			
			if($admission)
			{
				foreach($admission as $key => $value)
				{
					$discharge = array(
								'discharge_status' => 'Discharged'
									);
					$update_admission = $this->verifications_model->update('admission_report_test','code',$data['code'],$discharge);
				}
			}

			if($monitoring)
			{
				foreach($monitoring as $key => $value)
				{
					$discharge = array(
								'discharge_status' => 'Discharged'
								);
					$update_monitoring = $this->verifications_model->update('monitoring','code',$data['code'],$discharge);
				}
			}
		}
		else
		{
			$data['discharge_status'] = 'Running';
		}

		$user = $this->session->userdata('logged_in');
		$data['user'] = $user['name'];

		$save_monitoring = $this->verifications_model->register('monitoring',$data);

		if($save_monitoring)
		{
			if($_POST['submit'] == 'Discharge Patient')
			{
				$benefit_info = $this->verifications_model->getRecordById('benefits.benefitset_info',$data['benefit_set_id']);
				$benefit_set_name = $benefit_info[0]['benefit_set_name'];
				$table = str_replace(" ", "_", $benefit_set_name);
				$key1 = $_POST['benefit_set_id'];
				$key2 = 'In-Patient';
				$benefit_type = 'IP';
				$data['availment_type'] = $key2;
				$data['benefit_name'] = $benefit_info[0]['benefit_set_name'];

				$benefit_details = $this->verifications_model->getRecordByMultiField('benefits.benefitset_details',
																				array(
																					'benefit_set_id' => $key1,
																					'benefit_type' => $key2
																					));
				foreach($benefit_details as $key => $value)
				{
					$details[$value['registered_benefit']] = $this->verifications_model->getRecordByMultiField('benefits.basic_benefitss',
																											array(
																												'benefit_type' => $benefit_type,
																												'benefit_name' => $value['registered_benefit']
																												));
					$benefit_name[$value['registered_benefit']] = $value['registered_benefit'];
				}

				$diagnosis = $this->verifications_model->getRecordByField('availments_diagnosis','code',$_POST['code']);
				foreach($diagnosis as $key => $value)
				{
					$diagnosis_name[] = $value['diagnosis'];
				}
				$data['diagnosis'] = $diagnosis_name;

				$data['options'] = $benefit_name;
				$this->session->set_flashdata('result','<b>Successfully Updated Monitoring.</b>');

				$loadedViews = array(
								'verifications/verifications_register_LOA_view' => $data
								);
				$this->load->template($loadedViews,$this->header_links);
			}
			else
			{
				$this->session->set_flashdata('result','<b>Successfully Updated Monitoring Report.</b>');
				redirect('operations','refresh');
			}
		}
		else
		{
			$this->session->set_flashdata('result','<b>Update failed, something went wrong.</b>');
			redirect('operations','refresh');
		}
	}

	function viewAdmissionRecords($codeLOA)
	{
		$admissionReport = $this->verifications_model->getRecordByField('admission_report_test','code',$codeLOA);

		foreach($admissionReport as $key => $value)
		{
			$admissionReport[$key]['diagnosis'] = $this->verifications_model->getRecordByField('availments_diagnosis','code',$value['code']);
		}

		$data['admission'] = $admissionReport;
		$data['codeLOA'] = $codeLOA;
		$data['patient_name'] = $admissionReport[0]['patient_name'];

		$loadedViews = array(
						'operations/operations_view_admission_view' => $data
						);
		$this->load->template($loadedViews);
	}

	function viewMonitoringRecords($codeLOA)
	{
		$monitoringReport = $this->verifications_model->getRecordByField('monitoring','code',$codeLOA);

		foreach($monitoringReport as $key => $value)
		{
			$monitoringReport[$key]['diagnosis'] = $this->verifications_model->getRecordByField('availments_diagnosis','code',$codeLOA);
		}

		$data['monitoring'] = $monitoringReport;
		$data['codeLOA'] = $codeLOA;
		$data['patient_name'] = $monitoringReport[0]['patient_name'];

		$loadedViews = array(
						'operations/operations_view_monitoring_view' => $data
						);
		$this->load->template($loadedViews);
	}

	function edit($id)
	{
		$result = $this->verifications_model->getRecordById('availments_test',$id);
		$lab = $this->verifications_model->getRecordByField('lab_test_test', 'availments_id', $id);

		if($result)
		{
			foreach($result as $row)
			{
				$row['lab_test_test'] = $lab;
				$loadedViews = array(
					'verifications/verifications_edit_view' => $row
					);
				$this->load->template($loadedViews, $this->header_links);
			}
		}
	}

	function delete($id)
	{
		$delete = $this->verifications_model->delete('availments_test','id', $id);

		if($delete)
		{
			$deleteLab = $this->verifications_model->delete('lab_test_test','availments_id',$id);
			$deleteBenefitOthers = $this->verifications_model->delete('benefits_others', 'availment_id', $id);
			$benefitAsCharged = $this->verifications_model->delete('benefits_others_as_charged','availment_id', $id);
			$deleteIPOP = $this->verifications_model->delete('benefits_in-out_patient','availment_id',$id);
			$this->session->set_flashdata('result', 'Records Deleted.');
			redirect('verifications', 'refresh');
		}
	}

	function printLOA($code)
	{
		$loa = $this->verifications_model->getRecordByField('availments_test','code',$code);

		$availments_diagnosis = $this->verifications_model->getRecordByField('availments_diagnosis','code',$code);
		foreach($availments_diagnosis as $key => $value)
		{
			$diagnosis[] = $value['diagnosis'];
		}

		$benefits_in_out_patient = $this->verifications_model->getRecordByField('benefits_in-out_patient','code',$code);
		$in_out = array();
		if($benefits_in_out_patient)
		{
			foreach($benefits_in_out_patient as $key => $value)
			{
				if($value['availed_amount'] <= 0.00 && $value['availed_as-charged'] > 0)
				{
					$in_out[] = array('benefit_name'=>$value['benefit_name'],
								'availed_amount'=>$value['availed_as-charged']);
				}
				if($value['availed_as-charged'] <= 0.00 && $value['availed_amount'] > 0)
				{
					$in_out[] = array('benefit_name'=>$value['benefit_name'],
								'availed_amount'=>$value['availed_amount']);
				}
				if($value['availed_as-charged'] <= 0.00 && $value['availed_amount'] <= 0.00)
				{
					$in_out[] = array('benefit_name'=>$value['benefit_name'],
								'availed_amount'=>'');
				}
			}
		}

		$benefits_others = $this->verifications_model->getRecordByField('benefits_others','code',$code);
		$others = array();
		if($benefits_others)
		{
			foreach($benefits_others as $key => $value)
			{
				if($value['availed_amount'] > 0.00)
				{
					$others[] = array('benefit_name'=>$value['benefit_name'],
								'availed_amount'=>$value['availed_amount']);
				}
				if($value['availed_amount'] <= 0.00)
				{
					$others[] = array('benefit_name'=>$value['benefit_name'],
								'availed_amount'=>'');
				}
			}
		}

		$benefits_others_as_charged = $this->verifications_model->getRecordByField('benefits_others_as_charged','code',$code);
		$as_charged = array();
		if($benefits_others_as_charged)
		{
			foreach($benefits_others_as_charged as $key => $value)
			{
				if($value['availed_amount'] > 0.00)
				{
					$as_charged[] = array('benefit_name'=>$value['benefit_name'],
									'availed_amount'=>$value['availed_amount']);
				}
				if($value['availed_amount'] <= 0)
				{
					$as_charged[] = array('benefit_name'=>$value['benefit_name'],
									'availed_amount'=>'');
				}
			}
		}
		$benefits = array_merge($in_out,$others,$as_charged);

		foreach($loa as $row)
		{
			$row['diagnosis'] = $diagnosis;
			$row['benefits'] = $benefits;
			$this->load->view('verifications/verifications_print_loa',$row);

			switch ($row['availment_type'])
			{
				case 'In-Patient':
					$type = 'IP';
					break;
				case 'Out-Patient':
					$type = 'OP';
					break;
				case 'In and Out Patient':
					$type = 'IPOP';
					break;
				default:
					break;
			}
		}

		$date = date('d/m/Y');
		// PRINT TO PDF
		$html = $this->output->get_output();
		
		// Load library
		$this->load->library('dompdf_gen');
		
		// Convert to PDF
		$this->dompdf->load_html($html);
		$this->dompdf->render();
		$this->dompdf->stream('LOA-'.$type.'-'.$date."-".$code.".pdf",array('Attachment'=>0)); //convert 	
	}

	function specialLOA($id)
	{
		$result = $this->verifications_model->getRecordById('patient', $id);
		$patientCompIns = $this->verifications_model->getRecordByField('patient_company_insurance', 'patient_id',$id);
		$compIns = $this->verifications_model->getRecordById('company_insurance', $patientCompIns[0]['company_insurance_id']);
		$comCode = $this->verifications_model->getRecordByField('company', 'name', $compIns[0]['company']);
		$insCode = $this->verifications_model->getRecordByField('insurance', 'name', $compIns[0]['insurance']);

		//BENEFITS DETAILS AND INFO
		$benefitset_info = $this->verifications_model->getRecordByMultiField('benefits.benefitset_info',
																		array(
																			'compins_id' => $patientCompIns[0]['company_insurance_id'],
																			'level' => $result[0]['level'],
																			'cardholder_type' => $result[0]['cardholder_type']));

		if($benefitset_info == FALSE)
		{
			$benefitset_info = $this->verifications_model->getRecordByFieldsAndLike('benefits.benefitset_info',
																		array(
																			'compins_id' => $patientCompIns[0]['company_insurance_id'],
																			'level' => $result[0]['level'],
																			'cardholder_type' => $result[0]['cardholder_type']));
		}

		foreach($benefitset_info as $key => $value)
		{
			@$member = $this->verifications_model->getRecordByField('benefits.'.str_replace(" ","_",$value['benefit_set_name']), 'patient_id', $id);
			if($member)
			{
				$benefit_set_name = $value['benefit_set_name'];
				$benefit_set_id = $value['id'];
				@$benefitset_details = $this->verifications_model->getRecordByField('benefits.benefitset_details', 'benefit_set_id', $value['id']);
			}
		}

		foreach($benefitset_details as $key => $value)
		{
			$availment_type[$value['benefit_type']] = $value['benefit_type'];
		}
		
		//BENEFITS
		$result[0]['benefitset_info'] = $benefitset_info[0];
		$result[0]['options'] = $availment_type;
		$result[0]['benefit_name'] = $benefit_set_name;
		$result[0]['benefit_set_id'] = $benefit_set_id;

		$result[0]['compins'] = $compIns[0];
		$result[0]['company_code'] = $comCode[0];
		$result[0]['insurance_code'] = $insCode[0];
		
		echo '<pre>';
		var_dump($result);
		$loadedViews = array(
							'verifications/verifications_register_exceeding_view' => $result[0]
							);
		$this->load->template($loadedViews, $this->header_links);
	}

	function registerSpecial()
	{
		unset($_POST['submit']);
		$this->form_validation->set_rules('hospital_name','Hospital Name','trim|required|xss_clean');
		$this->form_validation->set_rules('hospital_branch','Hospital Branch','trim|xss_clean');
		$this->form_validation->set_rules('availment_type', 'Availment Type','trim|required|xss_clean');
		$this->form_validation->set_rules('patient_name','Patient Name','trim|required|xss_clean');
		$this->form_validation->set_rules('patient_id','Patient ID','trim|required|xss_clean');
		$this->form_validation->set_rules('company_name','Company Name','trim|required|xss_clean');
		$this->form_validation->set_rules('insurance_name','Insurance Name','trim|required|xss_clean');
		$this->form_validation->set_rules('company_code','Company Code', 'trim|required|xss_clean');
		$this->form_validation->set_rules('insurance_code','Insurance Code', 'trim|required|xss_clean');
		$this->form_validation->set_rules('principal_name','Principal Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('benefit_set_id','Benefit Set Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('date_encoded','Date Encoded', 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('operations','refresh');
		}
		else
		{
			$user = $this->session->userdata('logged_in');
			$data = $_POST;
			$data['code'] = $this->code->setCode();
			$data['user'] = $user['name'];
			unset($data['notes'],$data['compins_id'],
				$data['exclusion_name'],$data['condition_name'],
				$data['benefit_set_id'],$data['principal_name']);

			$register = $this->verifications_model->register('availments_special',$data);
			if($register)
			{
				$this->session->set_flashdata('result','Successfully registered exceeding amount');
				redirect('operations','refresh');
			}
			else
			{
				$this->session->set_flashdata('result','Error in registration exceeding amount');
				redirect('operations','refresh');
			}
		}
	}
}
?>