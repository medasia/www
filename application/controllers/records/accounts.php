<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
session_start();
class Accounts extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('records_model','',TRUE);
		$this->load->helper('url');
		
		$session_data = $this->session->userdata('logged_in');

		if($session_data)
		{
			$this->header_links = $session_data;

			switch($session_data['usertype'])
			{
				case 'sysad':
				case 'admin_assoc':
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
		$session_data = $this->session->userdata('logged_in');
		
		if($session_data)
		{
			switch($session_data['usertype'])
			{
				case 'sysad':
				case 'admin_assoc':
				case 'claims':
					$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/accounts/accounts_register_view' => NULL,
							'records/accounts/accounts_search_view' => NULL
							);
					$this->load->template($loadedViews, $this->header_links);
				break;
				
				default:
					echo '<script>alert("You are not allowed to access this portion of the site!");</script>';
					redirect('','refresh');
				break;
			}
		}
	}

	function register()
	{
		$table = $_POST['table'];
		unset($_POST['submit'],$_POST['table']);

		switch($table)
		{
			case 'insurance':
				$this->form_validation->set_rules('name', 'Insurance', 'trim|required|xss_clean');
				$this->form_validation->set_rules('attention_name', 'Attention Name', 'trim|required|xss_clean');
				$this->form_validation->set_rules('attention_position', 'Attention Position', 'trim|required|xss_clean');
				$this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');
				$this->form_validation->set_rules('code', 'Code', 'trim|required|xss_clean');
				// $this->form_validation->set_rules('vendor_account', 'Vendor Account', 'trim|required|xss_clean');
				$this->form_validation->set_rules('billing_code', 'Billing Code', 'trim|required|xss_clean');
			break;

			case 'brokers':
				$this->form_validation->set_rules('name', 'Broker', 'trim|required|xss_clean');
				$this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');
				$this->form_validation->set_rules('contact_person', 'Contact Person', 'trim|required|xss_clean');
				$this->form_validation->set_rules('contact_no', 'Contact No.', 'trim|required|xss_clean');
			break;

			case 'company':
				$this->form_validation->set_rules('name', 'Company', 'trim|required|xss_clean');
				$this->form_validation->set_rules('code', 'Code', 'trim|required|xss_clean');

				$this->form_validation->set_rules('insurance', 'Insurance', 'trim|required|xss_clean');
				$this->form_validation->set_rules('broker_name', 'Broker', 'trim|required|xss_clean');
				$this->form_validation->set_rules('start', 'Start Date', 'trim|required|xss_clean|valid_date');
				$this->form_validation->set_rules('end', 'End Date', 'trim|required|xss_clean|valid_date');
				$this->form_validation->set_rules('notes','Notes/Remarks', 'trim|xss_clean');
			break;
			
			default:
				# code...
			break;
		}

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('records/accounts','refresh');
		}
		else
		{
			$data = $_POST;
			$date_created = date('Y-m-d');
			$data['date_created'] = $date_created;

			if($table == 'company')
			{
				unset($data['insurance'],$data['insurance_id'],$data['broker_name'],$data['broker_id'],$data['start'],$data['end'],$data['notes']);
				$tag = $_POST;
				unset($tag['name'],$tag['code']);
				$tag['company'] = $_POST['name'];
	
				$register = $this->records_model->register($table, $data);
				$tag['company_id'] = $register;
				$tag['date_created'] = $date_created;
				if($register)
				{
					$register_tag = $this->records_model->register('company_insurance', $tag);
				}
				$tag_message = ' and tagged Insurance.';
			}
			else
			{
				$register = $this->records_model->register($table, $data);
				$tag_message = '.';
			}


			if($register)
			{
				$this->session->set_flashdata('result', '<b>Succesfully registered '.ucfirst($table).$tag_message.'</b>');
				redirect('records/accounts', 'refresh');
			}
			else
			{
				$this->session->set_flashdata('result', '<b>Error in registering data, database error or duplicate data may occured.');
				redirect('records/accounts','refresh');
			}
		}
	}

	function verifyPassword()
	{
		$this->form_validation->set_rules('selMulti[]', 'Multiple Select', 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			echo "INPUT ERROR: All fields are required!!";
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/accounts', 'refresh');
		}
		else
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

		if($data['submit'] == 'Delete')
		{
			$count = 0;
			foreach($data['selMulti'] as $id)
			{
				$delete = $this->records_model->delete('brokers',$id);
				$count++;
			}
			if($delete)
			{
				$this->session->set_flashdata('result','Deleted '.$count.' Brokers.');
				redirect('records/accounts','refresh');
			}
		}
	}

	function view($table,$id)
	{
		$result = $this->records_model->getRecordById($table,$id);

		if($result)
		{

			foreach($result as $row)
			{
				switch($table)
				{
					case 'insurance':
						$compins = $this->records_model->getRecordByField('company_insurance', 'insurance_id', $id);
						foreach ($compins as $key => $value) {
							$memberCount = $this->records_model->getCountByField('patient_company_insurance', 'company_insurance_id', $value['id']);
							$compins[$key]['membercount'] = $memberCount;
							$company = $this->records_model->getRecordById('company', $value['company_id']);
							foreach ($company as $company_key => $company_value) {
								$compins[$key]['code'] = $company_value['code'];
							}
						}
						$row['company'] = $compins;
						$loadedViews = array(
									// 'records/records_header_view' => $this->header_links,
									'records/insurance/insurance_view_insurance_view' => $row,
									);
						break;

					case 'company':
						$loadedViews = array(
									// 'records/records_header_view' => $this->header_links,
									'records/company/company_view_company_view' => $row
									);
					break;

					case 'brokers':
                        $loadedViews = array(
                                    // 'records/records_header_view' => $this->header_links,
                                    'records/accounts/accounts_brokers_edit_delete_view' => $row
                                    );
                        break;
					
					default:
						# code...
						break;
				}
				$this->load->template($loadedViews);
			}
		}
		else
		{
			$this->session->set_flashdata('result','<b>Record not found, may be deleted or an error occured.</b>');
			redirect('records/accounts','refresh');
		}
	}

	function printBilling()
	{
		echo '<pre>';
		if($_POST['type'] == 'Company')
		{
			$this->form_validation->set_rules('insurance','Insurance Name','trim|xss_clean|required');
			$this->form_validation->set_rules('insurance_id','Insurance ID', 'trim|xss_clean|required');
			$this->form_validation->set_rules('date_requested','Date Requested','trim|xss_clean|required');
			$this->form_validation->set_rules('billing_request_number',' Billing Request Number','trim|xss_clean|required');
			$this->form_validation->set_rules('reference_number','Reference Number','trim|xss_clean|required');
			$this->form_validation->set_rules('prepared_by','Prepared By','trim|xss_clean|required');
			$this->form_validation->set_rules('prepared_by_position','Prepared By Position','trim|xss_clean|required');
			$this->form_validation->set_rules('received_by','Received By Position','trim|xss_clean|required');
			$this->form_validation->set_rules('received_by_position','Received By Position','trim|xss_clean|required');
			$this->form_validation->set_rules('type','Billing form Type','trim|xss_clean|required');
		}
		elseif($_POST['type'] == 'Members')
		{
			$this->form_validation->set_rules('insurance','Insurance Name','trim|xss_clean|required');
			$this->form_validation->set_rules('insurance_id','Insurance ID', 'trim|xss_clean|required');
			$this->form_validation->set_rules('date_requested','Date Requested','trim|xss_clean|required');
			$this->form_validation->set_rules('billing_request_number','Billing Reference Number','trim|xss_clean|required');
			$this->form_validation->set_rules('reference_number','Reference Number','trim|xss_clean|required');
			$this->form_validation->set_rules('prepared_by','Prepared By','trim|xss_clean|required');
			$this->form_validation->set_rules('prepared_by_position','Prepared By Position','trim|xss_clean|required');
			$this->form_validation->set_rules('received_by','Received By Position','trim|xss_clean|required');
			$this->form_validation->set_rules('received_by_position','Received By Position','trim|xss_clean|required');
			$this->form_validation->set_rules('type','Billing form Type','trim|xss_clean|required');
			$this->form_validation->set_rules('company','Company Name','trim|xss_clean|required');
			$this->form_validation->set_rules('company_id','Company ID','trim|xss_clean|required');
			$this->form_validation->set_rules('billing_attention_name','Attention Name','trim|xss_clean|required');
		}

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('records/accounts','refresh');
		}
		else
		{
			var_dump($_POST);
			unset($_POST['submit']);
			$date_created = date('Y-m-d');
			$accounts_billing = $_POST;

			if($accounts_billing['type'] == 'Company')
			{
				unset($accounts_billing['multiple_company'],$accounts_billing['multiple_ipop'],$accounts_billing['multiple_ip']
					,$accounts_billing['multiple_er'],$accounts_billing['multiple_dental'],$accounts_billing['multiple_ape']
					,$accounts_billing['multiple_declaration'],$accounts_billing['multiple_release'],$accounts_billing['multiple_effectivity']
					,$accounts_billing['multiple_validity'],$accounts_billing['multiple_remarks']);

				$accounts_billing['date_created'] = $date_created;
				$accounts_billing_id = $this->records_model->register('accounts_billing', $accounts_billing);

				if($accounts_billing_id)
				{
					foreach($_POST['multiple_company'] as $key => $value)
					{
						$accounts_billing_company = array(
												'accounts_billing_id' => $accounts_billing_id,
												'company' => $value,
												'ipop' => $_POST['multiple_ipop'][$key],
												'ip' => $_POST['multiple_ip'][$key],
												'er' => $_POST['multiple_er'][$key],
												'dental' => $_POST['multiple_dental'][$key],
												'ape' => $_POST['multiple_ape'][$key],
												'declaration_date' => $_POST['multiple_declaration'][$key],
												'release_date' => $_POST['multiple_release'][$key],
												'effectivity_date' => $_POST['multiple_effectivity'][$key],
												'validity_date' => $_POST['multiple_validity'][$key],
												'remarks' => $_POST['multiple_remarks'][$key],
												'date_created' => $date_created
												);
						$register_accounts_billing_id = $this->records_model->register('accounts_billing_company',$accounts_billing_company);
					}
				}

				if($_FILES['file'])
				{
					echo 'TRUE';
					$config['upload_path'] = '/home/dev/web/operations051513/files/uploads/attachments/accounts/billing/company';
					$config['allowed_types'] = '*';
					$config['file_name'] = $this->encrypt->sha1(md5($_FILES['file']['name'].now()));
					$this->load->library('upload', $config);
					$this->upload->initialize($config);

					$ref = $this->input->server('HTTP_REFERER', TRUE);

					if(!file_exists($config['upload_path']))
					{
						mkdir($config['upload_path'], 0777);
					}
					if(file_exists($config['upload_path'].$config['file_name']))
					{
						$this->session->set_flashdata('result','<b>ERROR: File already exists.');
						redirect($ref,'location');
					}
					else
					{
						$filetypes = array('application/pdf','application/x-pdf','application/x-download',
									'binary/octet-stream','application/unknown','application/force-download',
									'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip',
									'application/msword');

						foreach($filetypes as $key => $value)
						{
							if($_FILES['file']['type'] == $value)
							{
								$file_allowed = TRUE;
							}
						}
						var_dump($file_allowed);

						if($file_allowed == TRUE)
						{
							var_dump($this->upload->do_upload('file'));
							var_dump($_FILES);
							if($this->upload->do_upload('file'))
							{
								echo $this->upload->display_errors();
								$file = $this->upload->data();
								$user = $this->session->userdata('logged_in');

								$filedata = array(
								'attach_to' => 'attachments/accounts/billing/company',
								'attached_id' => $accounts_billing_id,
								'reference_number' => $_POST['reference_number'],
								'attached_by' => $user['name'],
								'hash' => $file['raw_name'],
								'filename' => $file['client_name'],
								'path' => $file['file_path'],
								'size' => $file['file_size'].' kB',
								'date_uploaded' => $date_created
								);

								$resultFileData = $this->records_model->register('attachments',$filedata);
							}
						}	
					}
				}

				$data = $_POST;
				$data['insurance'] = $this->records_model->getRecordById('insurance',$_POST['insurance_id']);
				$this->load->view('records/accounts/accounts_print_billing_company_view',$data);
				
				// //PRINT TO PDF
				// $html = $this->output->get_output();

				// //Load Library
				// $this->load->library('dompdf_gen');

				// //Convert to PDF
				// $pdf = "Billing-".$data['reference_number'];
				// $this->dompdf->load_html($html);
				// $this->dompdf->render();
				// $this->dompdf->stream($pdf.".pdf",array('Attachment'=>0));

			}
			if($accounts_billing['type'] == 'Members')
			{
				// var_dump($accounts_billing);
				unset($accounts_billing['multiple_patient'],$accounts_billing['multiple_medical_plan'],$accounts_billing['multiple_amount'],
					$accounts_billing['multiple_declaration'],$accounts_billing['multiple_effectivity'],$accounts_billing['multiple_validity'],
					$accounts_billing['multiple_remarks'],$accounts_billing['company'],$accounts_billing['company_id'],$accounts_billing['billing_attention_name']);
				
				$accounts_billing['date_created'] = $date_created;
				$accounts_billing_id = $this->records_model->register('accounts_billing', $accounts_billing);

				if($accounts_billing_id)
				{
					foreach($_POST['multiple_patient'] as $key => $value)
					{
						$accounts_billing_members = array(
												'accounts_billing_id' => $accounts_billing_id,
												'company' => $value,
												'members' => $_POST['multiple_patient'][$key],
												'medical_plan' => $_POST['multiple_medical_plan'][$key],
												'amount' => $_POST['multiple_amount'][$key],
												'declaration_date' => $_POST['multiple_declaration'][$key],
												'effectivity_date' => $_POST['multiple_effectivity'][$key],
												'validity_date' => $_POST['multiple_validity'][$key],
												'remarks' => $_POST['multiple_remarks'][$key],
												'date_created' => $date_created
												);
						$register_accounts_billing_id = $this->records_model->register('accounts_billing_members',$accounts_billing_members);
					}
				}

				$data = $_POST;
				$data['insurance'] = $this->records_model->getRecordById('insurance',$_POST['insurance_id']);
				$this->load->view('records/accounts/accounts_print_billing_members_view',$data);

				//PRINT TO PDF
				$html = $this->output->get_output();

				//Load Library
				$this->load->library('dompdf_gen');

				//Convert to PDF
				$pdf = "Billing-".$data['reference_number'];
				$this->dompdf->load_html($html);
				$this->dompdf->render();
				$this->dompdf->stream($pdf.".pdf",array('Attachment'=>0));
			}
		}
	}

	function displayBillingAccounts()
	{
		$result = $this->records_model->getAllRecords('accounts_billing');

		foreach ($result as $key => $value)
		{
			if($value['type'] == 'Company')
			{
				$accounts_billing_company = $this->records_model->getRecordByField('accounts_billing_company', 'accounts_billing_id', $value['id']);
				$result[$key]['account_billing'] = $accounts_billing_company;
			}
			else if($value['type'] == 'Members')
			{
				$accounts_billing_members = $this->records_model->getRecordByField('accounts_billing_members', 'accounts_billing_id', $value['id']);
				$result[$key]['account_billing'] = $accounts_billing_members;
			}
		}

		$data['billing_accounts_results'] = $result;

		// echo '<pre>';
		// var_dump($data);
		$loadedViews = array(
                            	'records/accounts/accounts_billing_view' => $data
                            );
		$this->load->template($loadedViews);
	}
}
?>