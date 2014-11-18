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
		$this->load->helper(array('benefit_helper','html'));
		
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

	function index() 
	{
		// DISPLAY IN REGISTERED BENEFITS
		$data['query'] = $this->benefit_model->getBenefits();

		// DISPLAY IN BENEFITS CREATION
		$data1['ip'] = $this->benefit_model->getIPRecords();
		$data1['op'] = $this->benefit_model->getOPRecords();
		$data1['ipop'] = $this->benefit_model->getIPOPRecords();

		// DISPLAY IN REGISTERED SET OF BENEFITS
		@$info['info'] = $this->benefit_model->getAllRecords('benefitset_info','benefit_set_name');

		foreach(@$info['info'] as $key => $value)
		{
			$compins = $this->benefit_model->getRecordByID('operations_new.company_insurance', $value['compins_id']);

			foreach($compins as $ckey => $cvalue)
			{
				$info['compinsname'][] = $cvalue['company']."-".$cvalue['insurance']." (".mdate('%M %d, %Y', mysql_to_unix($cvalue['start']))." - ".mdate('%M %d, %Y', mysql_to_unix($cvalue['end'])).")";
			}
		}
		// var_dump($info);
		$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/benefits/benefit_register_view' => $data,
							'records/benefits/benefit_create_view' => $data1,
							'records/benefits/benefit_set_view' => $info
							);
		$this->load->template($loadedViews, $this->header_links);
	}

	function register()
	{
		$this->form_validation->set_rules('benefit_type','Benefit Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('benefit_name', 'Benefit Name', 'trim|required|xss_clean|is_unique[basic_benefitss.benefit_name]');
		$this->form_validation->set_rules('days', 'Days', 'trim|xss_clean');
		$this->form_validation->set_rules('amount','Amount', 'trim|xss_clean');
		$this->form_validation->set_rules('as_charged', 'As Chaged', 'trim|xss_clean');
		$this->form_validation->set_rules('otherDetails[]', 'Other Details', 'trim|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{
			echo "INPUT ERROR: All fields are required or Duplicate Entry!!!";
			$this->session->set_flashdata('result', validation_error());
			redirect('records/benefits', 'refresh');
		}
		else
		{	
			$data = $_POST;
			$details = array($_POST['days'], $_POST['amount'], $_POST['as_charged']);
			$data['details'] = array_merge($details, $_POST['otherDetails']);
			$detailsUpCase = array_map('strtoupper', $data['details']);
			unset($data['submit'],$data['days'],$data['amount'],$data['as_charged'],$data['otherDetails']);
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
					'benefit_name' => strtoupper($_POST['benefit_name']),
					'details' => $details
					);
					$register = $this->benefit_model->register('basic_benefitss',$data);
				}
			}
			if($register)
			{
				$this->session->set_flashdata('result', 'Benefits successfully added!');
				echo "Benefits successfully added!";
				redirect('records/benefits');
			}
			else
			{
				$this->form_validation->set_message('register', 'Something went wrong!');
				echo "Something went wrong!";
				redirect('records/benefits');
			}
		}
	}

	// ADD CREATE METHOD HERE

	function confirm()
	{
		$this->form_validation->set_rules('level', 'Levels', 'trim|required|xss_clean');

		if($_POST['submit'] == 'Back' || $this->form_validation->run() == FALSE)
		{
			redirect('records/benefits');
		}
		elseif($_POST['submit'] == 'Confirm')
		{
			// var_dump($_POST);

			//DATA TO CREATE TABLE: table name and fields
			$tableName = $_POST['benefit_set_name'];
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
									// var_dump($details);
									$benefitset_details = $this->benefit_model->register('benefitset_details', $details);
								}
							}
						}
					}
					
					if($benefitset_details)
					{
						$this->session->set_flashdata('result', 'Set of Benefits successfully created!');
						echo "Set of Benefits successfully created!";
						redirect('records/benefits');
					}
					// var_dump($benefit_type);
					// var_dump($benefit);
					// var_dump($benefitset_info);
				}
			}
			else
			{
				$this->form_validation->set_message('confirm', 'Benefit set name already exist, use another name!');
				echo "Benefit set name already exist, use another name!";
				// redirect('records/benefits');
			}			
		}
	}

	function edit($benefit_name)
	{
		$benefit_name = rawurldecode($benefit_name);
		$data['query'] = $this->benefit_model->getEditBenefit('benefits',$benefit_name);
		$loadedViews = array(
						'records/records_header_view' => $this->header_links,
						'records/benefits/benefit_edit_view' => $data
						);
		$this->load->template($loadedViews, $this->header_links);
	}

	function update($benefit_name)
	{
		var_dump($_POST);
		$benefit_name = rawurldecode($benefit_name);
		//Validates inputs from user, checks for security flaws
		$this->form_validation->set_rules('id[]','ID', 'trim|required|xss_clean');
		$this->form_validation->set_rules('availment_type','Availment Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('benefit_name', 'Benefit Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('details[]', 'Details', 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{ //if validation had errors reroute to benefits edit with flashdata that contains the said errors
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/benefits/edit/'.$benefit_name, 'refresh');
		}
		else
		{
			$id = $this->input->post('id');
			$details = $this->input->post('details');

			foreach($id as $key => $id)
			{
				$data = array(
					'availment_type' => $this->input->post('availment_type'),
					'benefit_name' => strtoupper($this->input->post('benefit_name')),
					'details' => strtoupper($details[$key])
					);

				if($data['details'] == '')
				{
					$this->benefit_model->deleteByID($id);
				}

				$update = $this->benefit_model->update('benefits', $id, $data);
				var_dump($details[$key]);
			}

		if($update)
		{
			$this->session->set_flashdata('result', 'Succesfully edited benefit.');
			// redirect('records/benefits', 'refresh');
		}
		else
		{
			$this->form_validation->set_message('update', 'Something is wrong');
			return false;
		}
		}
	}

	function delete($category,$benefit_name)
	{
		$benefit_name = rawurldecode($benefit_name);
		$delete = $this->benefit_model->delete($category,$benefit_name);
		if($delete)
		{
			$this->session->set_flashdata('result','Deleted Benefits');
			redirect('records/benefits','refresh');
		}
	}
}
?>