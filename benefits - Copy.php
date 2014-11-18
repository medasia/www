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
		// Call data needed
		$data['query'] = $this->benefit_model->getBenefits();
		$data1['query1'] = $this->benefit_model->getIPRecords();
		$data1['query2'] = $this->benefit_model->getOPRecords();
		$data1['query3'] = $this->benefit_model->getIPOPRecords();

		$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/benefits/benefit_register_view' => $data,
							'records/benefits/benefit_create_view' => $data1
							);
		$this->load->template($loadedViews, $this->header_links);
	}

	function register()
	{
		$this->form_validation->set_rules('availment_type','Availment Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('benefit_name', 'Benefit Name', 'trim|required|xss_clean|is_unique[benefits.benefit_name]');
		$this->form_validation->set_rules('details[]', 'Details', 'trim|required|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{
			echo "INPUT ERROR: All fields are required or Duplicate Entry!!!";
			$this->session->set_flashdata('result', validation_error());
			redirect('records/benefits', 'refresh');
		}
		else
		{	
			foreach($this->input->post('details') as $details) 
			{
					$data = array(
					'availment_type' => $this->input->post('availment_type'),
					'benefit_name' => strtoupper($this->input->post('benefit_name')),
					'details' => strtoupper($details)
					);
					$register = $this->benefit_model->register('benefits',$data);				
			}

			if($register)
			{
				$this->session->set_flashdata('result', 'Benefits successfully added!');
				redirect('records/benefits');
			}
			else
			{
				$this->form_validation->set_message('register', 'Something is wrong');
				return FALSE;
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

	function create()
	{
		var_dump($_POST);
		$this->form_validation->set_rules('comp_insurance_id','Company Insurance ID', 'trim|required|xss_clean');
		$this->form_validation->set_rules('principal_type', 'Principal Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('rightIP[]', 'In Patient', 'trim|xss_clean');
		$this->form_validation->set_rules('rightOP[]', 'Out Patient', 'trim|xss_clean');
		$this->form_validation->set_rules('rightIP-OP[]','In/Out Patient', 'trim|xss_clean');
		$this->form_validation->set_rules('ben_name', 'Benefit Name', 'trim|required|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{
			echo "All fields are required!!!";
			$this->session->set_flashdata('result', validation_error());
			redirect('records/benefits', 'refresh');
		}
	// 	else
	// 	{
	// 		$data = array(
	// 					'comp_insurance_id' => $this->input->post('comp_insurance_id'),
	// 					'principal_type' => $this->input->post('principal_type'),
	// 					'ben_name' => $this->input->post('ben_name'),
	// 					'ip' => $this->input->post('rightIP[]'),
	// 					'op' => $this->input->post('rightOP[]'),
	// 					'ipop' => $this->input->post('rightIP-OP[]')
	// 					);
	// 					// $create = $this->benefit_model->register('create_benefits',$data);
	// 	}
	// 	$this->load->view('benefit_create_view2',$_POST);
	// 		var_dump($_POST);
	// }
		else
		{	
			$rightOPs = $this->input->post('rightOP');
			$rightIPs = $this->input->post('rightIP');

			if(count($rightIPs) >= count($rightOPs))
			{
				 $right = $rightIPs;
			}
			else
			{
				$right = $rightOPs;
			}
				$i=0;
				foreach($right as $row)
				{
					$i++;
						$data = array(
						'comp_insurance_id' => $this->input->post('comp_insurance_id'),
						'principal_type' => $this->input->post('principal_type'),
						'ben_name' => $this->input->post('ben_name'),
						'ip' => $row,
						'op' => $this->input->post('rightIP[]'),
						'ipop' => $this->input->post('rightIP-OP[]')
						);
						$create = $this->benefit_model->register('create_benefits',$data);				
				}
			if($create)
			{
				$this->session->set_flashdata('result', 'Benefits created successfully!');
				// redirect('records/benefits');
				$this->load->view('records/benefits/benefit_create_view2',$_POST);
				// var_dump($_POST);
			}
			else
			{
				$this->form_validation->set_message('register', 'Something is wrong');
				return FALSE;
			}
		}
	}
}
		// // var_dump($_POST);

		// $this->load->view('records/benefits/benefit_create_view2',$_POST);
		// var_dump($_POST);
?>