<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Benefits extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$benefitsDB = $this->load->database('benefits', TRUE);
		$this->load->library(array('table', 'form_validation'));
		$this->load->model('benefit_model','',TRUE);
		$this->load->model('databasecreate_model','', TRUE);
		$this->load->helper(array('benefit_helper','benefit_remarks_helper','html'));
		
		if($this->session->userdata('logged_in'))
		{	
			//set header links depending on logged in users in userdata session
			$this->header_links = $this->session->userdata('logged_in');

			$session_data = $this->session->userdata('logged_in');
			switch($session_data['usertype'])
			{
				case 'sysad':
				case 'admin_assoc':
				case 'ops':
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

	function index() 
	{
		// DISPLAY IN REGISTERED BENEFITS
		$data['query'] = $this->benefit_model->getBenefits();

		//DISPLAY REGISTERED OTHER CONDITIONS/EXCLUSIONS
		$others['condition'] = $this->benefit_model->getFullRecords('benefit_set_condition');
		$others['exclusion'] = $this->benefit_model->getFullRecords('benefit_set_exclusion');

		// DISPLAY IN BENEFITS CREATION
		$data1['ip'] = $this->benefit_model->getIPRecords();
		$data1['op'] = $this->benefit_model->getOPRecords();
		$data1['ipop'] = $this->benefit_model->getIPOPRecords();

		// DISPLAY IN REGISTERED SET OF BENEFITS
		@$info['info'] = $this->benefit_model->getAllRecords('benefitset_info','benefit_set_name');

		foreach(@$info['info'] as $key => $value)
		{
			$info['memberCount'][$key] = $this->benefit_model->getCountByField(str_replace(" ","_",$value['benefit_set_name']), 'benefitset_id', $value['id']);

			$compins = $this->benefit_model->getRecordByID('operations_new.company_insurance', $value['compins_id']);

			foreach($compins as $ckey => $cvalue)
			{
				$info['compinsname'][$key] = $cvalue['company']."-".$cvalue['insurance']." (".mdate('%M %d, %Y', mysql_to_unix($cvalue['start']))." - ".mdate('%M %d, %Y', mysql_to_unix($cvalue['end'])).")";
			}

			//CONDITIONS or EXCLUSION details
			$condition = $this->benefit_model->getRecordByField('benefit_set_condition','condition_name',$value['condition_name']);
			$exclusion = $this->benefit_model->getRecordByField('benefit_set_exclusion','exclusion_name',$value['exclusion_name']);

			if($condition)
			{
				foreach($condition as $value => $ckey)
				{
					$info['condition_details'][$key] = $ckey['condition_details'];
				}
			}
			if($exclusion)
			{
				foreach($exclusion as $value => $ekey)
				{
					$info['exclusion_details'][$key] = $ekey['exclusion_details'];
				}
			}
		}
		// var_dump($info);
		$user = $this->session->userdata('logged_in');
		if($user['usertype'] == 'sysad')
		{
			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/benefits/benefit_register_view' => $data,
							'records/benefits/benefit_register_others_view' => $others,
							'records/benefits/benefit_create_view' => $data1,
							'records/benefits/benefit_set_admin_view' => $info
							);
			$this->load->template($loadedViews, $this->header_links);
		}
		elseif($user['usertype'] == 'admin_assoc'
			|| $user['usertype'] == 'ops'
			|| $user['usertype'] == 'claims')
		{
			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/benefits/benefit_register_view' => $data,
							'records/benefits/benefit_register_others_view' => $others,
							'records/benefits/benefit_create_view' => $data1,
							'records/benefits/benefit_set_view' => $info
							);
			$this->load->template($loadedViews, $this->header_links);
		}
		else
		{
			echo '<script>alert("You are not allowed to access this portion of the site!");</script>';
			redirect('','refresh');
		}
	}

	function register()
	{
		$this->form_validation->set_rules('benefit_type','Benefit Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('benefit_name', 'Benefit Name', 'trim|required|xss_clean|is_unique[basic_benefitss.benefit_name]');
		$this->form_validation->set_rules('days', 'Days', 'trim|xss_clean');
		$this->form_validation->set_rules('amount','Amount', 'trim|xss_clean');
		$this->form_validation->set_rules('as_charged', 'As Charged', 'trim|xss_clean');
		$this->form_validation->set_rules('amount_per_day','Amount Per Day', 'trim|xss_clean');
		$this->form_validation->set_rules('otherDetails[]', 'Other Details', 'trim|xss_clean');
		$this->form_validation->set_rules('remarks', 'Remarks', 'trim|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{
			echo "INPUT ERROR: All fields are required or Duplicate Entry!!!";
			$this->session->set_flashdata('result', validation_error());
			redirect('records/benefits', 'refresh');
		}
		else
		{	
			$data = $_POST;
			$details = array($_POST['days'], $_POST['amount'], $_POST['as_charged'],$_POST['amount_per_day'],$_POST['by_modalities']);#,$_POST['per_illness']);
			$data['details'] = array_merge($details, $_POST['otherDetails']);
			$detailsUpCase = array_map('strtoupper', $data['details']);
			unset($data['submit'],$data['days'],$data['amount'],$data['as_charged'],$data['otherDetails'],$data['remarks'],$data['by_modalities']);#,$data['amount_per_day'],$data['per_illness']);
			$user = $this->session->userdata('logged_in');
			// var_dump($data);

			foreach(array_unique($detailsUpCase) as $details)
			{
				if($details == '')
				{
					unset($details);
				}
				else
				{
					$data = array(
					'benefit_type' => strtoupper($_POST['benefit_type']),
					'benefit_name' => str_replace(',','-',strtoupper($_POST['benefit_name'])),
					'details' => $details,
					'user' => $user['name']
					);
					$register = $this->benefit_model->register('basic_benefitss',$data);
				}
			}
			if($register)
			{
				if($_POST['remarks'] != '')
				{
					$remarks['benefit_name'] = str_replace(',','-',strtoupper($_POST['benefit_name']));
					$remarks['remarks'] = $_POST['remarks'];
					$remarks['user'] = $user['name'];
					$registerRemarks = $this->benefit_model->register('benefits_remarks', $remarks);
				}
				
				$this->session->set_flashdata('result', 'Successfully registered. Next step, create Sets of Benefits');
				redirect('records/benefits');
			}
			else
			{
				$this->form_validation->set_message('result', 'Something went wrong!');
				echo "Something went wrong!";
				redirect('records/benefits');
			}
		}
	}

	function create()
	{
		// var_dump($_POST);
		$this->form_validation->set_rules('company_insurance','Company Insurance', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cardholder_type', 'Cardholder Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('rightIP[]', 'In Patient', 'trim|xss_clean');
		$this->form_validation->set_rules('rightOP[]', 'Out Patient', 'trim|xss_clean');
		$this->form_validation->set_rules('rightIP-OP[]','In/Out Patient', 'trim|xss_clean');
		$this->form_validation->set_rules('benefit_name', 'Benefit Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('condition_name', 'Condition Name','trim|xss_clean');
		$this->form_validation->set_rules('exclusion_name', 'Exclusion Name', 'trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			echo "All fields are required!!!";
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/benefits', 'refresh');
		}
		else
		{
			if(isset($_POST['rightIP']))
			{
				foreach($_POST['rightIP'] as $key => $value)
				{
					$ip = $this->benefit_model->getRecordByField('benefits.basic_benefitss','benefit_name',$value);

					foreach($ip as $ipDetails)
					{
						$column[$ipDetails['benefit_type'].".".$value.".".str_replace(" ","_", $ipDetails['details'])] = $value.".".str_replace(" ", "_", $ipDetails['details']);
						$value = str_replace(" ", "_", $value);
						$ipFields[$ipDetails['benefit_type']."#".$value."#".str_replace(" ", "_", $ipDetails['details'])] = array('type'=>'VARCHAR', 'constraint'=>'200');
					}
				}
				// var_dump($column);
			}
			else
			{
				$ipFields = array();
			}

			if(isset($_POST['rightOP']))
			{
				foreach($_POST['rightOP'] as $key => $value)
				{
					$op = $this->benefit_model->getRecordByField('benefits.basic_benefitss','benefit_name',$value);

					foreach($op as $opDetails)
					{
						$column[$opDetails['benefit_type'].".".$value.".".str_replace(" ","_", $opDetails['details'])] = $value.".".str_replace(" ", "_", $opDetails['details']);
						$value = str_replace(" ", "_", $value);
						$opFields[$opDetails['benefit_type']."#".$value."#".str_replace(" ", "_", $opDetails['details'])] = array('type'=>'VARCHAR', 'constraint'=>'200');
					}
				}
			}
			else
			{
				$opFields = array();
			}
		
			if(isset($_POST['rightIP-OP']))
			{
				foreach($_POST['rightIP-OP'] as $key => $value)
				{
					$ipop = $this->benefit_model->getRecordByField('benefits.basic_benefitss','benefit_name',$value);

					foreach($ipop as $ipopDetails)
					{
						$column[$ipopDetails['benefit_type'].".".$value.".".str_replace(" ","_", $ipopDetails['details'])] = $value.".".str_replace(" ", "_", $ipopDetails['details']);
						$value = str_replace(" ", "_", $value);
						$ipopFields[$ipopDetails['benefit_type']."#".$value."#".str_replace(" ", "_", $ipopDetails['details'])] = array('type'=>'VARCHAR', 'constraint'=>'200');
					}
				}		
			}
			else
			{
				$ipopFields = array();
			}

			$defaultFields = array(
							'benefitset_id' => array(
										'type' => 'INT',
										'constraint' => '200'),
							'patient_id' => array(
										'type'=>'INT',
										'constraint'=>'200'),
							'cardholder_type' => array(
										'type'=>'VARCHAR',
										'constraint'=>'200'),
							'level' => array(
										'type'=>'VARCHAR',
										'constraint'=>'200'),
							'maximum_benefit_limit' => array(
										'type'=>'DOUBLE',
										'constraint'=>'20,2')
							);
		
			$fields = array_merge($defaultFields,$ipFields,$opFields,$ipopFields);

			// USED TO GET THE LEVEL IN EVERY COMPANY-INSURANCE
			$id = $_POST['compins_id'];
			$compinsname = $this->benefit_model->getRecordByID('operations_new.company_insurance', $id);
			$result = $this->benefit_model->getRecordByField('operations_new.patient_company_insurance', 'company_insurance_id', $id, 'patient_id');
		
			foreach($result as $key => $value)
			{
				$levels[$key] = $this->benefit_model->getLevelByID('operations_new.patient', $value['patient_id'], 'level');
			}

			// FORM DETAILS, TO BE PASSED ON CONFIRM VIEW
			$data['compins_name'] = $_POST['company_insurance'];
			$data['cardholder_type'] = $_POST['cardholder_type'];
			$data['compins_id'] = $_POST['compins_id'];
			$data['benefit_set_name'] = $_POST['benefit_name'];

			if(isset($_POST['rightIP']))
			{
				$data['ip'] = $_POST['rightIP'];
			}
			if(isset($_POST['rightOP']))
			{
				$data['op'] = $_POST['rightOP'];
			}
			if(isset($_POST['rightIP-OP']))
			{
				$data['ipop'] = $_POST['rightIP-OP'];
			}

			$data['fields'] = $fields;
			$data['levels'] = $levels;

			$data['condition_name'] = $_POST['condition_name'];
			$data['condition_details'] = $this->benefit_model->getRecordByField('benefit_set_condition','condition_name',$_POST['condition_name']);
			$data['exclusion_name'] = $_POST['exclusion_name'];
			$data['exclusion_details'] = $this->benefit_model->getRecordByField('benefit_set_exclusion','exclusion_name',$_POST['exclusion_name']);

			$loadedViews =array(
							'records/records_header_view' => $this->header_links,
							'records/benefits/benefit_confirm_view' => $data
							);
			$this->load->template($loadedViews, $this->header_links);			
		}
	}

	function confirm()
	{
		$this->form_validation->set_rules('level', 'Levels', 'trim|required|xss_clean');
		$this->form_validation->set_rules('benefit_limit_type','Benefit Limit Type','trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('records/benefits');
		}
		else
		{
			// var_dump($_POST);

			//DATA TO CREATE TABLE: table name and fields
			$tableName = str_replace(" ","_",$_POST['benefit_set_name']);
			$fields = $_POST['fields'];
			
			$createTable = $this->databasecreate_model->createTable($tableName,$fields);
			
			if($createTable)
			{
				// INFORMATION TO BE STORED IN benefitset_info table
				$info['compins_id'] = $_POST['compins_id'];
				$info['benefit_set_name'] = $_POST['benefit_set_name'];
				$info['level'] = $_POST['level'];
				$info['cardholder_type'] = $_POST['cardholder_type'];
				$info['maximum_benefit_limit'] = $_POST['maximum_benefit_limit'];
				$info['condition_name'] = $_POST['condition_name'];
				$info['exclusion_name'] = $_POST['exclusion_name'];
				$info['benefit_limit_type'] = $_POST['benefit_limit_type'];
				$info['date_created'] = date('Y-m-d H:i:s');
				$info['plan_type'] = $_POST['plan_type'];

				$user = $this->session->userdata('logged_in');
				$info['user'] = $user['name'];

				$restriction = $this->benefit_model->checkRestriction('benefitset_info', $info['compins_id'], $info['level'], $info['cardholder_type']);

				if($restriction)
				{
					$deleteTable = $this->databasecreate_model->deleteTable($tableName);
					$this->session->set_flashdata('result', '<b>The combination of Cardholder Type and Level for this Company - Insurance already has a Benefit.</b>');
					redirect('records/benefits', 'refresh');
				}
				else
				{
					$benefitset_info = $this->benefit_model->register('benefitset_info', $info);

					if($benefitset_info)
					{
						//DETAILS TO BE STORED IN benefitset_details table

						if(isset($_POST['ip']))
						{
							$ip[] = $_POST['ip'];

							foreach($_POST['ipDetails'] as $key => $value)
							{
								$benefit[$_POST['ip']] = $_POST['ipDetails'];								
							}
						}
						else
						{
							$ip = array();
						}

						if(isset($_POST['op']))
						{
							$op[] = $_POST['op'];

							foreach($_POST['opDetails'] as $key => $value)
							{
								$benefit[$_POST['op']] = $_POST['opDetails'];
							}
						}
						else
						{
							$op = array();
						}

						if(isset($_POST['ipop']))
						{
							$ipop[] = $_POST['ipop'];

							foreach($_POST['ipopDetails'] as $key => $value)
							{
								$benefit[$_POST['ipop']] = $_POST['ipopDetails'];
							}
						}
						else
						{
							$ipop = array();
						}
					
						$benefit_type = array_merge($ip, $op, $ipop);

						foreach($benefit_type as $key => $value)
						{
							foreach($benefit as $bkey => $bvalue)
							{
								if($bkey == $value)
								{
									foreach($bvalue as $registered_benefit)
									{
										$details = array(
											'benefit_set_id' => $benefitset_info,
											'benefit_type' => $value,
											'registered_benefit' => $registered_benefit
										);
										
										$benefitset_details = $this->benefit_model->register('benefitset_details', $details);
									}
								}
							}
						}
					
						if($benefitset_details)
						{
							$this->session->set_flashdata('result', 'Sets of Benefits successfully saved. Next step, View sets of Benefits.');
							echo "Set of Benefits successfully created!";
							redirect('records/benefits');
						}
					}
				}
			}
			else
			{
				$this->form_validation->set_message('result', 'Benefit set name already exist, use another name or character exceed!');
				echo "Benefit set name already exist, use another name!";
				redirect('records/benefits');
			}			
		}
	}

	function view($id)
	{
		$benefitset['info'] = $this->benefit_model->getRecordByID('benefitset_info',$id);
		$compins_id = $benefitset['info'][0]['compins_id'];
		$compins = $this->benefit_model->getRecordByID('operations_new.company_insurance',$compins_id);
		$benefitset['compinsname'] = $compins[0]['company']." - ".$compins[0]['insurance']." (".mdate('%M %d, %Y', mysql_to_unix($compins[0]['start']))." - ".mdate('%M %d, %Y', mysql_to_unix($compins[0]['end'])).")";
		$benefitset['details'] = $this->benefit_model->getRecordByField('benefitset_details', 'benefit_set_id', $id);
		$benefitset['fields'] = $this->benefit_model->getAllRecords(str_replace(" ","_",$benefitset['info'][0]['benefit_set_name']),'id');

		foreach($benefitset['details'] as $key => $value)
		{
			if($benefitset['details'][$key]['benefit_type'] == 'In-Patient')
			{
				$benefitset['ip'][$value['registered_benefit']] = $this->benefit_model->getRecordByField('basic_benefitss','benefit_name',$value['registered_benefit'],'details');
			}
			if($benefitset['details'][$key]['benefit_type'] == 'Out-Patient')
			{
				$benefitset['op'][$value['registered_benefit']] = $this->benefit_model->getRecordByField('basic_benefitss','benefit_name',$value['registered_benefit'],'details');
			}
			if($benefitset['details'][$key]['benefit_type'] == 'In and Out Patient')
			{
				$benefitset['ipop'][$value['registered_benefit']] = $this->benefit_model->getRecordByField('basic_benefitss','benefit_name',$value['registered_benefit'],'details');
			}
		}

		$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/benefits/benefit_set_view_view' => $benefitset
							);
		$this->load->template($loadedViews, $this->header_links);
	}

	function addOrUpdate()
	{
		$compins_id = $_POST['compins_id'];
		$level = $_POST['level'];
		$cardholder_type = $_POST['cardholder_type'];
		$benefitset_id = $_POST['benefitset_id'];
		$table = str_replace(" ", "_",$_POST['table']);

		$data['benefitset_info'] = $this->benefit_model->getRecordByID('benefitset_info', $benefitset_id);

		$compinsname = $this->benefit_model->getRecordByID('operations_new.company_insurance',$compins_id);
		$data['compins_id'] = $compinsname[0]['id'];
		$data['compins_name'] = $compinsname[0]['company']." - ".$compinsname[0]['insurance']." (".mdate('%M %d, %Y', mysql_to_unix($compinsname[0]['start']))." - ".mdate('%M %d, %Y', mysql_to_unix($compinsname[0]['end'])).")";
			
		$result = $this->benefit_model->getRecordByField('operations_new.patient_company_insurance','company_insurance_id',$compins_id, 'patient_id');
			
		foreach($result as $key => $value)
		{
			$members[$key] = $this->benefit_model->getRecordByID('operations_new.patient', $value['patient_id']);
		}

		foreach($members as $key => $value)
		{
			if($cardholder_type == 'Principal and Dependent')
			{
				if($value[0]['level'] == $level)
				{
					$alreadyMember = $this->benefit_model->getRecordByField($table, 'patient_id', $value[0]['id']);
					
					if($alreadyMember == FALSE)
					{
						$data['patients'][] = array('label' => $value[0]['lastname'].', '.$value[0]['firstname'].' '.$value[0]['middlename'], 'patient_id' => $value[0]['id']);
					}
				}
			}
			else
			{
				if(($value[0]['level'] == $level AND $value[0]['cardholder_type'] == strtoupper($cardholder_type)) OR ($value[0]['level'] == $level AND $value[0]['cardholder_type'] == ucfirst($cardholder_type)))
				{
					$alreadyMember = $this->benefit_model->getRecordByField($table,'patient_id',$value[0]['id']);
						
					if($alreadyMember == FALSE)
					{
						$data['patients'][] = array('label' => $value[0]['lastname'].', '.$value[0]['firstname'].' '.$value['middlename'], 'patient_id' => $value[0]['id']);
					}
				}
			}
		}

		$benefitset_details = $this->benefit_model->getRecordByField('benefitset_details','benefit_set_id', $data['benefitset_info'][0]['id']);

		foreach($benefitset_details as $key => $value)
		{
			if($benefitset_details[$key]['benefit_type'] == 'In-Patient')
			{
				$data['ip'][$value['registered_benefit']] = $this->benefit_model->getRecordByField('basic_benefitss','benefit_name', $value['registered_benefit'],'details');	
			}
			if($benefitset_details[$key]['benefit_type'] == 'Out-Patient')
			{
				$data['op'][$value['registered_benefit']] = $this->benefit_model->getRecordByField('basic_benefitss','benefit_name', $value['registered_benefit'], 'details');
			}
			if($benefitset_details[$key]['benefit_type'] == 'In and Out Patient')
			{
				$data['ipop'][$value['registered_benefit']] = $this->benefit_model->getRecordByField('basic_benefitss','benefit_name', $value['registered_benefit'], 'details');
			}
		}
			// var_dump($data);
			
		$benefit_members = $this->benefit_model->getAllRecords($table,'patient_id');

		foreach($benefit_members as $key => $value)
		{
			$ben_members[$key] = $this->benefit_model->getRecordByID('operations_new.patient', $value['patient_id']);
		}
		foreach($ben_members as $key => $value)
		{
			$data['members'][] = array('label'=> $value[0]['lastname'].', '.$value[0]['firstname'].' '.$value[0]['middlename'], 'patient_id'=>$value[0]['id']);
		}

		$fields = $this->benefit_model->getAllRecords($table,'id');
		$data['fields'] = $fields[0];

		if($_POST['submit'] == 'Add Members')
		{
			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/benefits/benefit_add_member_view'=> $data
							);
			$this->load->template($loadedViews, $this->header_links);
		}
		elseif($_POST['submit'] == 'Edit')
		{
			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/benefits/benefit_update_member_view'=>$data
							);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function addMembers()
	{
		// var_dump($_POST);
		unset($_POST['leftMembers']);
		$benefitset_id = $_POST['benefitset_id'];
		$table = str_replace(" ", "_", $_POST['table']);
		$compins_id = $_POST['compins_id'];
		$level = $_POST['level'];
		$cardholder_type = $_POST['cardholder_type'];

		$this->form_validation->set_rules('rightMembers[]', 'Select Patient', 'trim|required|xss_clean|is_unique['.$table.'.patient_id]');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/benefits', 'refresh');
		}
		else
		{
			$field = $_POST;
			unset($field['currentMembers'],$field['compins_id']);

			foreach($_POST['rightMembers'] as $patient_id)
			{
				unset($field['submit'],$field['table'],$field['rightMembers']);
				$field['patient_id'] = $patient_id;

				$register = $this->benefit_model->register($table,$field);
			}

			if($register)
			{
				$benefitset_name = $this->benefit_model->getRecordByID('benefitset_info', $benefitset_id);
				$data['info'] = $benefitset_name;
 				$table = str_replace(" ","_",$benefitset_name[0]['benefit_set_name']);
				$result = $this->benefit_model->getRecordByField($table,'benefitset_id',$benefitset_id);
				$compinsname = $this->benefit_model->getRecordById('operations_new.company_insurance', $benefitset_name[0]['compins_id']);
				$data['id'] = $compinsname[0]['id'];
				$data['compinsname'] = $compinsname[0]['company']." - ".$compinsname[0]['insurance']." (".mdate('%M %d, %Y', mysql_to_unix($compinsname[0]['start']))." - ".mdate('%M %d, %Y', mysql_to_unix($compinsname[0]['end'])).")";
				$data['name'] = $table;
				$data['fields'] = $this->benefit_model->getAllRecords($table,'id');

				if(isset($result))
				{
					foreach($result as $key => $value)
					{
						@$members[$key] = $this->benefit_model->getRecordByID('operations_new.patient', $value['patient_id']);
					}
				}

				$data['patients'] = @$members;

				$data['details'] = $this->benefit_model->getRecordByField('benefitset_details','benefit_set_id', $benefitset_id);

				foreach($data['details'] as $key => $value)
				{
					if($data['details'][$key]['benefit_type'] == 'In-Patient')
					{
						$data['ip'][$value['registered_benefit']] = $this->benefit_model->getRecordByField('basic_benefitss','benefit_name',$value['registered_benefit'],'details');
					}
					if($data['details'][$key]['benefit_type'] == 'Out-Patient')
					{
						$data['op'][$value['registered_benefit']] = $this->benefit_model->getRecordByField('basic_benefitss','benefit_name',$value['registered_benefit'],'details');
					}
					if($data['details'][$key]['benefit_type'] == 'In and Out Patient')
					{
						$data['ipop'][$value['registered_benefit']] = $this->benefit_model->getRecordByField('basic_benefitss','benefit_name',$value['registered_benefit'],'details');
					}
				}

				$this->session->set_flashdata('result', 'Members of benefit successfully added.');

				$loadedViews = array(
					'records/records_header_view' => $this->header_links,
					'records/benefits/benefit_members_view'=> $data
					);
				$this->load->template($loadedViews, $this->header_links);
			}				
		}
	}

	function update()
	{
		unset($_POST['leftMembers']);
		$benefitset_id = $_POST['benefitset_id'];
		$table = str_replace(" ", "_", $_POST['table']);
		$compins_id = $_POST['compins_id'];
		$level = $_POST['level'];
		$cardholder_type = $_POST['cardholder_type'];
	
		$this->form_validation->set_rules('currentMembers[]', 'Current Members', 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/benefits/','refresh');
		}
		else
		{
			unset($_POST['rightMembers'],$_POST['submit'],$_POST['table'],$_POST['compins_id']);

			foreach($_POST['currentMembers'] as $key => $value)
			{
				unset($_POST['currentMembers']);
				$data = $_POST;
				$update = $this->benefit_model->update($table,'patient_id',$value,$data);
			}

			if($update)
			{
				$data['benefitset_info'] = $this->benefit_model->getRecordByID('benefitset_info', $benefitset_id);

				$compinsname = $this->benefit_model->getRecordByID('operations_new.company_insurance',$compins_id);
				$data['compins_id'] = $compinsname[0]['id'];
				$data['compins_name'] = $compinsname[0]['company']." - ".$compinsname[0]['insurance']." (".mdate('%M %d, %Y', mysql_to_unix($compinsname[0]['start']))." - ".mdate('%M %d, %Y', mysql_to_unix($compinsname[0]['end'])).")";
			
				$result = $this->benefit_model->getRecordByField('operations_new.patient_company_insurance','company_insurance_id',$compins_id, 'patient_id');

				foreach($result as $key => $value)
				{
					$members[$key] = $this->benefit_model->getRecordByID('operations_new.patient', $value['patient_id']);
				}

				foreach($members as $key => $value)
				{
					if(($value[0]['level'] == $level and $value[0]['cardholder_type'] == strtoupper($cardholder_type)) OR ($value[0]['level'] == $level and $value[0]['cardholder_type'] == ucfirst($cardholder_type)))
					{
						$data['patients'][]= array('label' => $value[0]['lastname'].', '.$value[0]['firstname']." ".$value[0]['middlename'], 'patient_id'=>$value[0]['id']);
					}
				}

				$benefitset_details = $this->benefit_model->getRecordByField('benefitset_details','benefit_set_id', $data['benefitset_info'][0]['id']);

				foreach($benefitset_details as $key => $value)
				{
					if($benefitset_details[$key]['benefit_type'] == 'In-Patient')
					{
						$data['ip'][$value['registered_benefit']] = $this->benefit_model->getRecordByField('basic_benefitss','benefit_name', $value['registered_benefit'],'details');	
					}
					if($benefitset_details[$key]['benefit_type'] == 'Out-Patient')
					{
						$data['op'][$value['registered_benefit']] = $this->benefit_model->getRecordByField('basic_benefitss','benefit_name', $value['registered_benefit'], 'details');
					}
					if($benefitset_details[$key]['benefit_type'] == 'In and Out Patient')
					{
						$data['ipop'][$value['registered_benefit']] = $this->benefit_model->getRecordByField('basic_benefitss','benefit_name', $value['registered_benefit'], 'details');
					}
				}
			
				$benefit_members = $this->benefit_model->getAllRecords($table,'patient_id');

				foreach($benefit_members as $key => $value)
				{
					$ben_members[$key] = $this->benefit_model->getRecordByID('operations_new.patient', $value['patient_id']);
				}
				foreach($ben_members as $key => $value)
				{
					$data['members'][] = array('label'=> $value[0]['lastname'].', '.$value[0]['firstname'].' '.$value[0]['middlename'], 'patient_id'=>$value[0]['id']);
				}

				$fields = $this->benefit_model->getAllRecords($table,'id');
				$data['fields'] = $fields[0];

				$this->session->set_flashdata('result', 'Benefit successfully updated.');
				$loadedViews = array(
						'records/records_header_view' => $this->header_links,
						'records/benefits/benefit_update_member_view'=> $data
						);
				$this->load->template($loadedViews, $this->header_links);
			}
		}
	}

	function viewMembers($id)
	{
		$data['info'] = $this->benefit_model->getRecordByID('benefitset_info', $id);
		$table = str_replace(" ","_", $data['info'][0]['benefit_set_name']);
		$result = $this->benefit_model->getRecordByField($table,'benefitset_id',$id);
		$compinsname = $this->benefit_model->getRecordByID('operations_new.company_insurance', $data['info'][0]['compins_id']);
		$data['id'] = $compinsname[0]['id'];
		$data['compinsname'] = $compinsname[0]['company']." - ".$compinsname[0]['insurance']." (".mdate('%M %d, %Y', mysql_to_unix($compinsname[0]['start']))." - ".mdate('%M %d, %Y', mysql_to_unix($compinsname[0]['end'])).")";
		$data['name'] = $table;
		$data['fields'] = $this->benefit_model->getAllRecords($table,'id');

		if(isset($result))
		{
			foreach($result as $key => $value)
			{
				@$members[$key] = $this->benefit_model->getRecordByID('operations_new.patient', $value['patient_id']);
			}
		}

		$data['patients'] = @$members;

		$data['details'] = $this->benefit_model->getRecordByField('benefitset_details', 'benefit_set_id', $id);

		foreach($data['details'] as $key => $value)
		{
			if($data['details'][$key]['benefit_type'] == 'In-Patient')
			{
				$data['ip'][$value['registered_benefit']] = $this->benefit_model->getRecordByField('basic_benefitss','benefit_name',$value['registered_benefit'],'details');
			}
			if($data['details'][$key]['benefit_type'] == 'Out-Patient')
			{
				$data['op'][$value['registered_benefit']] = $this->benefit_model->getRecordByField('basic_benefitss','benefit_name',$value['registered_benefit'],'details');
			}
			if($data['details'][$key]['benefit_type'] == 'In and Out Patient')
			{
				$data['ipop'][$value['registered_benefit']] = $this->benefit_model->getRecordByField('basic_benefitss','benefit_name',$value['registered_benefit'],'details');
			}
		}

		$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/benefits/benefit_members_view'=> $data
							);
		$this->load->template($loadedViews, $this->header_links);
	}

	function delete($benefit_type, $benefit_name)
	{
		$benefit_name = rawurldecode($benefit_name);
		$delete = $this->benefit_model->delete($benefit_type,$benefit_name);
		$deleteRemarks = $this->benefit_model->deleteByField('benefits_remarks','benefit_name',$benefit_name);

		if($delete)
		{
			$this->session->set_flashdata('result', 'Deleted Benefits');
			redirect('records/benefits');
		}
	}

	function deleteBenefitSet($id,$benefit_set_name)
	{
		$data['id'] = $id;
		$data['field'] = str_replace(" ","_", rawurldecode($benefit_set_name));
		$data['location'] = 'records/benefits';

		$loadedViews = array(
						'records/records_header_view' => $this->header_links,
						'records/verify_pass_view' => $data
						);
		$this->load->template($loadedViews,$this->header_links);
	}

	function proceedDelete()
	{
		$data = $this->session->flashdata('data');
		$id = $data['id'];
		$tableName = $data['field'];

		$deleteInfo = $this->benefit_model->deleteByID('benefitset_info',$id);

		if($deleteInfo)
		{
			$deleteDetails = $this->benefit_model->deleteByField('benefitset_details','benefit_set_id',$id);

			if($deleteDetails)
			{
				$deleteTable = $this->databasecreate_model->deleteTable($tableName);
			}
		}

		if($deleteInfo && $deleteDetails)
		{
			$this->session->set_flashdata('result','<b>Successfully Deleted Benefit Set.</b>');
			redirect('records/benefits','refresh');
		}
		else
		{
			$this->session->set_flashdata('result','<b>Something went wrong, failed to delete Benefit Set.</b>');
			redirect('records/benefits','refresh');
		}
	}

	function multiSelect()
	{
		$this->form_validation->set_rules('selMulti[]', 'Multiple Select', 'trim|xss_clean');

		if($_POST['submit'] == 'Back' OR $this->form_validation->run() == FALSE)
		{
			redirect('records/benefits');
		}
		elseif($_POST['submit'] == 'Delete')
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
		$tableid = $data['id'];
		$info = $this->benefit_model->getRecordByID('benefitset_info', $tableid);
		$table = str_replace(" ","_", $info[0]['benefit_set_name']);

		$count = 0;
		foreach($data['selMulti'] as $id)
		{
			$delete = $this->benefit_model->deleteByField($table, 'patient_id', $id);
			$count++;
		}
		if($delete)
		{
			$this->session->set_flashdata('result', $count.' member/s successfully deleted');
			redirect('records/benefits/viewMembers/'.$tableid);
		}
	}

	function edit($benefit_name)
	{
		$benefit_name = rawurldecode($benefit_name);
		$data['query'] = $this->benefit_model->getEditBenefit('basic_benefitss',$benefit_name);
		$loadedViews = array(
						'records/records_header_view' => $this->header_links,
						'records/benefits/benefit_edit_view' => $data
						);
		$this->load->template($loadedViews, $this->header_links);
	}

	function addOthers()
	{
		$user = $this->session->userdata('logged_in');
		// IF name IS SET, THE details ALSO MUST BE SET
		if(isset($_POST['condition_name']))
		{
			$this->form_validation->set_rules('condition_name','Condition Name','trim|xss_clean');
			$this->form_validation->set_rules('condition_details','Condition Details','trim|xss_clean');
		}
		if(isset($_POST['exclusion_name']))
		{
			$this->form_validation->set_rules('exclusion_name','Exclusion Name','trim|xss_clean');
			$this->form_validation->set_rules('exclusion_details','Exclusion Details','trim|xss_clean');
		}

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('records/benefits','refresh');
		}
		else
		{
			// IF BOTH name AND details ARE SET IT WILL BE REGISTERED ELSE, IT WILL BE UNSET
			if(isset($_POST['condition_name']) AND isset($_POST['condition_details']))
			{
				$data = array(
						'condition_name' => $_POST['condition_name'],
						'condition_details' => $_POST['condition_details'],
						'user' => $user['name']
						);
				$condition = $this->benefit_model->register('benefit_set_condition',$data);
			}
			else
			{
				unset($_POST['condition_name'],$_POST['condition_details']);
			}

			if(isset($_POST['exclusion_name']) AND isset($_POST['exclusion_details']))
			{
				$data = array(
						'exclusion_name' => $_POST['exclusion_name'],
						'exclusion_details' => $_POST['exclusion_details'],
						'user'	=>	$user['name']
						);
				$exclusion = $this->benefit_model->register('benefit_set_exclusion',$data);
			}
			else
			{
				unset($_POST['exclusion_name'], $_POST['exclusion_details']);
			}

			if($condition || $exclusion)
			{
				$this->session->set_flashdata('result','Successfully Registered Other Condition/Exclusion');
				redirect('records/benefits','refresh');
			}
			else
			{
				$this->session->set_flashdata('result','Failed to Register Other Condition/Exclusion');
				redirect('records/benefits','refresh');
			}
		}
	}

	function editCondition($id)
	{
		$result = $this->benefit_model->getRecordByID('benefit_set_condition',$id);
		if($result)
		{
			foreach($result as $row)
			{
				$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/benefits/benefit_edit_condition_view' => $row
								);
				$this->load->template($loadedViews,$this->header_links);
			}
		}
		else
		{
			$this->session->set_flashdata('result','<b>Records Not Found</b>');
			redirect('records/benefits','refresh');
		}
	}

	function editExclusion($id)
	{
		$result = $this->benefit_model->getRecordByID('benefit_set_exclusion',$id);
		if($result)
		{
			foreach($result as $row)
			{
				$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/benefits/benefit_edit_exclusion_view' => $row
								);
				$this->load->template($loadedViews,$this->header_links);
			}
		}
		else
		{
			$this->session->set_flashdata('result','<b>Record not found.</b>');
			redirect('records/benefits','refresh');
		}
	}

	function deleteCondition($id)
	{
		$delete = $this->benefit_model->deleteByID('benefit_set_condition',$id);
		if($delete)
		{
			$this->session->set_flashdata('result','Record successfully deleted.');
			redirect('records/benefits','refresh');
		}
	}

	function deleteExclusion($id)
	{
		$delete = $this->benefit_model->deleteByID('benefit_set_exclusion',$id);
		if($delete)
		{
			$this->session->set_flashdata('result','Record successfully deleted');
			redirect('records/benefits','refresh');
		}
	}
}
?>