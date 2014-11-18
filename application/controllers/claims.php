<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Claims extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('claims_model','',TRUE);
		$this->load->library(array('table','form_validation','code'));

		if($this->session->userdata('logged_in'))
		{
			//set header links depending on logged in users in userdata session
			$this->header_links = $this->session->userdata('logged_in');

			$session_data = $this->session->userdata('logged_in');
			switch($session_data['usertype'])
			{
				case 'sysad':
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
		if($this->session->userdata('logged_in'))
		{
			$session_data = $this->session->userdata('logged_in');
			$data = $session_data;

			switch($data['usertype'])
			{
				case 'sysad':
				case 'claims':
					$loadedViews = array(
							'claims/claims_search_view' => NULL
							);
					$this->load->template($loadedViews,$this->header_links);
				break;

				default:
					echo '<script>alert("You are not allowed to access this portion of the site!");</script>';
					edirect('','refresh');
			}
		}
	}

	function receiving()
	{
		$this->form_validation->set_rules('keyword','Keyword','trim|xss_clean');
		$this->form_validation->set_rules('identity','Identity','trim|xss_clean|required');
		$this->form_validation->set_rules('date_from','Date From','trim|xss_clean|required');
		$this->form_validation->set_rules('date_to','Date To','trim|xss_clean|required');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('claims','refresh');
		}
		else
		{
			$keyword = $_POST['keyword'];
			$field = $_POST['identity'];
			$start = $_POST['date_from'];
			$end = $_POST['date_to'];

			$received = $this->claims_model->getReceived('availments_test',$keyword,$field,$start,$end);

			if($received)
			{
				foreach($received as $key => $value)
				{
					$labs = $this->claims_model->getRecordByField('lab_test_test','availments_id',$value['id']);
					$labInfo = array('lab_test','amount');

					if($labs)
					{
						foreach($labs as $lkey => $lval)
						{
							foreach($labInfo as $field)
							{
								$received[$key]['lab_test_test'][$lkey][$field] = $lval[$field];
							}
						}
					}
					else
					{
						$received[$key]['lab_test_test'] = NULL;
					}
					$received[$key]['benefits_in-out_patient'] = $this->claims_model->getRecordByField('benefits_in-out_patient','availment_id',$value['id']);
					$received[$key]['benefits_others'] = $this->claims_model->getRecordByField('benefits_others','availment_id',$value['id']);
					$received[$key]['benefits_others_as_charged'] = $this->claims_model->getRecordByField('benefits_others_as_charged','availment_id',$value['id']);
					$received[$key]['diagnosis'] = $this->claims_model->getRecordByField('availments_diagnosis','code',$value['code']);
					$received[$key]['specialist'] = $this->claims_model->getRecordByField('admission_specialist','code',$value['code']);
				}
			}

			$data['received'] = $received;

			$loadedViews = array(
						'claims/claims_search_view' => NULL,
						'claims/claims_received_view' => $data
						);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function edit($id)
	{
		// var_dump($id);

		$edit = $this->claims_model->getRecordById('availments_test',$id);

		$labs = $this->claims_model->getRecordByField('lab_test_test','availments_id',$id);
		$labInfo = array('lab_test','amount');

		if($labs)
		{
			foreach($labs as $lkey => $lval)
			{
				foreach($labInfo as $field)
				{
					$edit[0]['lab_test_test'][$lkey][$field] = $lval[$field];
				}
			}
		}
		else
		{
			$edit[0]['lab_test_test'] = NULL;
		}

		$edit[0]['benefits_in_out_patient'] = $this->claims_model->getRecordByField('benefits_in-out_patient','availment_id',$id);
		$edit[0]['benefits_others'] = $this->claims_model->getRecordByField('benefits_others','availment_id',$id);
		$edit[0]['benefits_others_as_charged'] = $this->claims_model->getRecordByField('benefits_others_as_charged','availment_id',$id);
		$edit[0]['diagnosis'] = $this->claims_model->getRecordByField('availments_diagnosis','code',$edit[0]['code']);
		$edit[0]['specialists'] = $this->claims_model->getRecordByField('admission_specialist','code',$edit[0]['code']);

		if($edit)
		{
			foreach($edit as $row)
			{
				$loadedViews = array(
							'claims/claims_received_record_view' => $row
							);
				$this->load->template($loadedViews);
			}
		}
		else
		{
			$this->session->set_flashdata('results','<b>Record not found, may be deleted or an error occured.</b>');
			redirect('claims','refresh');
		}
	}

	function received()
	{
		$this->form_validation->set_rules('sel_multi[]','Selection','trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('claims','refresh');
		}
		else
		{
			$count = 0;
			$data = array(
					'claims_status'=>'RECEIVED',
					'claims_dateofrecieve' => date('Y-m-d H:i:s')
					);
			foreach($_POST['sel_multi'] as $id)
			{
				$count++;
				$update_claims_status = $this->claims_model->update('availments_test','id',$id,$data);
			}

			if($update_claims_status)
			{
				$this->session->set_flashdata('result','Received '.$count.' patients.');
				redirect('claims','refresh');
			}
		}
	}

	function billing()
	{
		$this->form_validation->set_rules('insurance_billing','Insurance Name','xss_clean|trim|required');
		$this->form_validation->set_rules('billing_date_from','Billing Date From','xss_clean|trim|required');
		$this->form_validation->set_rules('billing_date_to','Billing Date To','xss_clean|trim|required');
		$this->form_validation->set_rules('availment_type','Availment Type','xss_clean|trim|required');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('claims','refresh');
		}
		else
		{
			$date_from = $_POST['billing_date_from'];
			$date_to = $_POST['billing_date_to'];
			$availment_type = $_POST['availment_type'];
			$insurance_name = $_POST['insurance_billing'];
			$received = 'RECEIVED';
			
			$billing = $this->claims_model->getBilling('availments_test', $insurance_name,$availment_type,$received,$date_from,$date_to);

			if($billing)
			{
				foreach($billing as $key => $value)
				{
					$labs = $this->claims_model->getRecordByField('lab_test_test','availments_id',$value['id']);
					$labInfo = array('lab_test','amount');

					if($labs)
					{
						foreach($labs as $lkey => $lval)
						{
							foreach($labInfo as $field)
							{
								$billing[$key]['lab_test_test'][$lkey][$field] = $lval[$field];
							}
						}
					}
					else
					{
						$billing[$key]['lab_test_test'] = NULL;
					}
					$billing[$key]['benefits_in-out_patient'] = $this->claims_model->getRecordByField('benefits_in-out_patient','availment_id',$value['id']);
					$billing[$key]['benefits_others'] = $this->claims_model->getRecordByField('benefits_others','availment_id',$value['id']);
					$billing[$key]['benefits_others_as_charged'] = $this->claims_model->getRecordByField('benefits_others_as_charged','availment_id',$value['id']);
					$billing[$key]['diagnosis'] = $this->claims_model->getRecordByField('availments_diagnosis','code',$value['code']);
					$billing[$key]['specialist'] = $this->claims_model->getRecordByField('admission_specialist','code',$value['code']);
				}
			}
			$data['billing'] = $billing;

			$loadedViews = array(
							'claims/claims_search_view' => NULL,
							'claims/claims_billing_view' => $data
							);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function patientBilling($id)
	{
		// IN - PATIENT
		// AVAILMENT DETAILS
		$availments = $this->claims_model->getRecordById('availments_test',$id);
		$data['availments'] = $availments;

		$code = $availments[0]['company_code'].'-'.$availments[0]['insurance_code'].'-'.date('Y');

		// DOCTOR
		$physician = $this->claims_model->getRecordByField('admission_report_test','code',$availments[0]['code']);
		// echo '<pre>';
		// var_dump($physician);
		$data['doctor'] = $physician[0]['physician'];
			
		// DIAGNOSIS
		$diagnosis = $this->claims_model->getRecordByField('availments_diagnosis','code',$availments[0]['code']);
		foreach($diagnosis as $key => $value)
		{
			$data['diagnosis'][] = $value['diagnosis'];
		}

		$benefits['benefits_in-out_patient'] = $this->claims_model->getRecordByField('benefits_in-out_patient','availment_id',$id);
		$benefits['benefits_others'] = $this->claims_model->getRecordByField('benefits_others','availment_id',$id);
		$benefits['benefits_others_as_charged'] = $this->claims_model->getRecordByField('benefits_others_as_charged','availment_id',$id);

		foreach($benefits as $key => $value)
		{
			if($value == FALSE)
			{
				unset($value);
			}
			else
			{
				// PLAN / BENEFIT SET NAME
				$benefit_set_name = $this->claims_model->getRecordById('benefits.benefitset_info',$value[0]['benefit_set_id']);
				foreach($benefit_set_name as $bkey => $bvalue)
				{
					$data['plan'] = $bvalue['benefit_set_name'];
				}

				// For Total Amount of Availments
				foreach($value as $akey => $avalue)
				{
					if($avalue['availed_amount'] != 0.00)
					{
						$data['hospital_bills'][] = $avalue['availed_amount'];
					}
					if($avalue['availed_as-charged'] != 0.00)
					{
						$data['hospital_bills'][] = $avalue['availed_as-charged'];
					}
				}
			}
		}
		//INSURANCE DETAILS
		$data['insurance'] = $this->claims_model->getRecordByField('insurance','name',$availments[0]['insurance_name']);
		// var_dump($data);
		
		$to_print = $data;
		// var_dump($to_print);

		$loadedViews = array(	
						'claims/claims_bill_form_view' => $to_print,
						'claims/claims_bill_preview_view' => $data
						);
		$this->load->template($loadedViews, $this->header_links);
	}

	function printBilling()
	{
		$this->form_validation->set_rules('date','Date','trim|xss_clean|required');
		$this->form_validation->set_rules('attachments','Attachments','trim|xss_clean');
		$this->form_validation->set_rules('prepared_by','Prepared By','trim|xss_clean|required');
		$this->form_validation->set_rules('checked_by','Checked By','trim|xss_clean|required');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('claims','refresh');
		}
		else
		{
			foreach($_POST['availments'] as $value)
			{
				foreach($value as $key => $row)
				{
					$availments[$key] = $row;
				}
			}

			foreach($_POST['insurance'] as $value)
			{
				foreach($value as $key => $row)
				{
					$insurance[$key] = $row;
				}
			}

			$claims_status = array(
							'claims_status' => 'BILLED'
							);
			$update_claims_status = $this->claims_model->update('availments_test','code',$availments['code'],$claims_status);

			$billing_code = str_replace(' ','_', $availments['insurance_code'].'-'.$availments['company_code']);

			$check_code = $this->claims_model->getRecordByField('billings_compins','billing_code',$billing_code);

			if($check_code == FALSE)
			{
				if($availments['availment_type'] == 'In-Patient' || $availments['availment_type'] == 'In and Out Patient')
				{
					$billings_compins = array(
									'insurance_code' => $availments['insurance_code'],
									'company_code' => $availments['company_code'],
									'count_ip' => '0001',
									'count_op' => '0000',
									'billing_code' => $billing_code,
									);
					$billings_register = $this->claims_model->register('billings_compins',$billings_compins);

					$billing_code_print = $billing_code.'-IP#'.date('Y').'-'.$billings_compins['count_ip'];
				}

				if($availments['availment_type'] == 'Out-Patient')
				{
					$billings_compins = array(
									'insurance_code' => $availments['insurance_code'],
									'company_code' => $availments['company_code'],
									'count_ip' => '0000',
									'count_op' => '0001',
									'billing_code' => $billing_code,
									);
					$billings_register = $this->claims_model->register('billings_compins',$billings_compins);

					$billing_code_print = $billing_code.'-OP#'.date('Y').'-'.$billings_compins['count_op'];
				}
				
				if($billings_register)
				{
					$billings_ip = array(
									'insurance_name' => $availments['insurance_name'],
									'company_name' => $availments['company_name'],
									'patient_name' => $availments['patient_name'],
									'loa_code' => $availments['code'],
									'claims_code' => $billing_code_print,
									'print_date' => $_POST['date']
									);
					$billings_ip = $this->claims_model->register('billings_details',$billings_ip);
				}
			}
			else
			{
				if($availments['availment_type'] == 'In-Patient' || $availments['availment_type'] == 'In and Out Patient')
				{
					$count = array(
							'count_ip' => $check_code[0]['count_ip']+1
							);
					$update_count = $this->claims_model->update('billings_compins','id',$check_code[0]['id'],$count);
				
					$billing_code_print = $check_code[0]['billing_code'].'-IP#'.date('Y').'-'.$update_count[0]['count_ip'];

					if($update_count)
					{
						$billings_ip = array(
									'insurance_name' => $availments['insurance_name'],
									'company_name' => $availments['company_name'],
									'patient_name' => $availments['patient_name'],
									'loa_code' => $availments['code'],
									'claims_code' => $billing_code_print,
									'print_date' => $_POST['date']
									);
						$billings_ip = $this->claims_model->register('billings_details',$billings_ip);
					}
				}

				if($availments['availment_type'] == 'Out-Patient')
				{
					$count = array(
							'count_op' => $check_code[0]['count_op']+1
							);
					$update_count = $this->claims_model->update('billings_compins','id',$check_code[0]['id'],$count);
				
					$billing_code_print = $check_code[0]['billing_code'].'-OP#'.date('Y').'-'.$update_count[0]['count_op'];

					if($update_count)
					{
						$billings_ip = array(
									'insurance_name' => $availments['insurance_name'],
									'company_name' => $availments['company_name'],
									'patient_name' => $availments['patient_name'],
									'loa_code' => $availments['code'],
									'claims_code' => $billing_code_print,
									'print_date' => $_POST['date']
									);
						$billings_ip = $this->claims_model->register('billings_details',$billings_ip);
					}
				}
			}

			// PRINT
			$print['availments'][] = $availments;
			$print['doctor'] = $_POST['doctor'];
			$print['hospital_bills'] = $_POST['hospital_bills'];
			$print['plan'] = $_POST['plan'];
			$print['insurance'][] = $insurance;
			$print['diagnosis'] = $_POST['diagnosis'];
			$print['date'] = $_POST['date'];
			$print['attachments'] = $_POST['attachments'];
			$print['checked_by'] = $_POST['checked_by'];
			$print['prepared_by'] = $_POST['prepared_by'];
			$print['claims_code'] = $billing_code_print;

			// REPRINTING
			$reprint['claims_code'] = $billing_code_print;
			$reprint['attachments'] = $_POST['attachments'];
			$reprint['checked_by'] = $_POST['checked_by'];
			$reprint['prepared_by'] = $_POST['prepared_by'];
			$reprint['print_date'] = $_POST['date'];

			$reprint_register = $this->claims_model->register('billings_reprint',$reprint);

			// var_dump($availments);
			$this->load->view('claims/claims_bill_print_view',$print);

			// PRINT TO PDF
			$html = $this->output->get_output();
		
			// Load library
			$this->load->library('dompdf_gen');
		
			// Convert to PDF
			$this->dompdf->load_html($html);
			$this->dompdf->render();
			$this->dompdf->stream($billing_code_print.".pdf",array('Attachment'=>0)); //convert 
		}
	}

	function ipBilling()
	{
		$this->form_validation->set_rules('insurance_ip_billing','Insurance Name','xss_clean|required|trim');
		$this->form_validation->set_rules('ip_billing_date_from','Billing Date From','xss_clean|trim|required');
		$this->form_validation->set_rules('ip_billing_date_to','Billing Date To','xss_clean|trim|required');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('claims','refresh');
		}
		else
		{
			$insurance = $_POST['insurance_ip_billing'];
			$date_start = $_POST['ip_billing_date_from'];
			$date_to = $_POST['ip_billing_date_to'];

			$billed = $this->claims_model->getBilled('billings_details',$insurance,'insurance_name',$date_start,$date_to);

			if($billed)
			{
				foreach($billed as $bkey => $bvalue)
				{
					$availments[$bkey]['availments'] = $this->claims_model->getRecordByField('availments_test','code',$bvalue['loa_code']);

					if($availments)
					{
						foreach($availments[$bkey]['availments'] as $akey => $avalue)
						{
							$labs = $this->claims_model->getRecordByField('lab_test_test','availments_id',$avalue['id']);
							$labInfo = array('lab_test','amount');

							if($labs)
							{
								foreach($labs as $lkey => $lval)
								{
									foreach($labInfo as $field)
									{
										$availments[$bkey]['lab_test_test'][$lkey][$field] = $lval[$field];
									}
								}
							}
						else
						{
							$availments[$bkey]['lab_test_test'] = NULL;
						}
							$availments[$bkey]['benefits_in-out_patient'] = $this->claims_model->getRecordByField('benefits_in-out_patient','availment_id',$avalue['id']);
							$availments[$bkey]['benefits_others'] = $this->claims_model->getRecordByField('benefits_others','availment_id',$avalue['id']);
							$availments[$bkey]['benefits_others_as_charged'] = $this->claims_model->getRecordByField('benefits_others_as_charged','availment_id',$avalue['id']);
							$availments[$bkey]['diagnosis'] = $this->claims_model->getRecordByField('availments_diagnosis','code',$avalue['code']);
						}
					}		
				}
			}
			$data['availments'] = $availments;
			$data['billed'] = $billed;

			$loadedViews = array(
						'claims/claims_search_view' => NULL,
						'claims/claims_billed_ip_view' => $data
						);
			$this->load->template($loadedViews,$this->header_links);
		}
	}

	function summarized()
	{
		$this->form_validation->set_rules('sel_multi[]','Selection','trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('claims','refresh');
		}
		else
		{
			foreach($_POST['sel_multi'] as $key => $value)
			{
				$data['sel_multi'] = $_POST['sel_multi'];

				//BILLING DETAILS FROM INSURANCE
				$data['insurance_billing'][$key] = $this->claims_model->getRecordById('billings_details',$value);

				foreach($data['insurance_billing'][$key] as $ikey => $ivalue)
				{
					// INSURANCE DETAILS
					$insu_details = $this->claims_model->getRecordByField('insurance','name',$ivalue['insurance_name']);
					foreach($insu_details as $row)
					{
						$data['insurance_details'][$key] = $row;
					}

					// AVAILMENTS DETAILS
					$data['availments'][$key] = $this->claims_model->getRecordByField('availments_test','code',$ivalue['loa_code']);

					foreach($data['availments'][$key] as $akey => $avalue)
					{
						// BENEFITS TOTAL AMOUNT DETAILS
						$benefits['benefits_in-out_patient'] = $this->claims_model->getRecordByField('benefits_in-out_patient','availment_id',$avalue['id']);
						$benefits['benefits_others'] = $this->claims_model->getRecordByField('benefits_others','availment_id',$avalue['id']);
						$benefits['benefits_others_as_charged'] = $this->claims_model->getRecordByField('benefits_others_as_charged','availment_id',$avalue['id']);

						foreach($benefits as $bkey => $bvalue)
						{
							if($bvalue == FALSE)
							{
								unset($bvalue);
							}
							else
							{
								foreach($bvalue as $bakey => $bavalue)
								{
									$total_amount[$key] = 0.00;
									if($bavalue['availed_amount'] != 0.00)
									{
										$amount[] = $bavalue['availed_amount'];
									}
									if($bavalue['availed_as-charged'] != 0.00)
									{
										$amount[] = $bavalue['availed_as-charged'];
									}

									foreach($amount as $amkey => $amvalue)
									{
										$total_amount[$key] += $amvalue;
									}
								}
								$data['total_amount'] = $total_amount;
							}
						}
					}
				}
			}
			$loadedViews = array(
							'claims/claims_summary_form_view' => $data
							);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function printSummary()
	{
		$this->form_validation->set_rules('invoice','Invoice Number','trim|xss_clean|required|is_unique[billings_topsheet.invoice_number]');
		$this->form_validation->set_rules('due_date','Due Date','trim|xss_clean|required');
		$this->form_validation->set_rules('prepared_by','Prepared By','trim|xss_clean|required');
		$this->form_validation->set_rules('noted_by','Noted By','trim|xss_clean|required');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('claims','refresh');
		}
		else
		{
			// var_dump($_POST);
			$data = $_POST;
			unset($data['grand_total']);
			foreach($_POST['sel_multi'] as $key => $value)
			{
				$data['sel_multi'] = $_POST['sel_multi'];

				//BILLING DETAILS FROM INSURANCE
				$data['insurance_billing'][$key] = $this->claims_model->getRecordById('billings_details',$value);

				foreach($data['insurance_billing'][$key] as $ikey => $ivalue)
				{
					// INSURANCE DETAILS
					$insu_details = $this->claims_model->getRecordByField('insurance','name',$ivalue['insurance_name']);
					foreach($insu_details as $row)
					{
						$data['insurance_details'][$key] = $row;
					}

					// AVAILMENTS DETAILS
					$data['availments'][$key] = $this->claims_model->getRecordByField('availments_test','code',$ivalue['loa_code']);

					foreach($data['availments'][$key] as $akey => $avalue)
					{
						//UPDATE STATUS TO SUMMARIZED
						$update = array(
							'claims_status'=>'SUMMARIZED'
								);
						$update_claims_status = $this->claims_model->update('availments_test','id',$avalue['id'],$update);

						// BENEFITS TOTAL AMOUNT DETAILS
						$benefits['benefits_in-out_patient'] = $this->claims_model->getRecordByField('benefits_in-out_patient','availment_id',$avalue['id']);
						$benefits['benefits_others'] = $this->claims_model->getRecordByField('benefits_others','availment_id',$avalue['id']);
						$benefits['benefits_others_as_charged'] = $this->claims_model->getRecordByField('benefits_others_as_charged','availment_id',$avalue['id']);

						foreach($benefits as $bkey => $bvalue)
						{
							if($bvalue == FALSE)
							{
								unset($bvalue);
							}
							else
							{
								foreach($bvalue as $bakey => $bavalue)
								{
									$total_amount[$key] = 0.00;
									if($bavalue['availed_amount'] != 0.00)
									{
										$amount[] = $bavalue['availed_amount'];
									}
									if($bavalue['availed_as-charged'] != 0.00)
									{
										$amount[] = $bavalue['availed_as-charged'];
									}

									foreach($amount as $amkey => $amvalue)
									{
										$total_amount[$key] += $amvalue;
									}
								}
								$data['total_amount'] = $total_amount;
							}
						}
					}
				}
			}

			if($data['availments'][0][0]['availment_type'] == 'In-Patient' || $data['availments'][0][0]['availment_type'] == 'In and Out Patient')
			{
				$topsheet['availment_type'] = 'In-Patient';
			}
			elseif($data['availments'][0][0]['availment_type'] == 'Out-Patient')
			{
				$topsheet['availment_type'] = 'Out-Patient';
			}

			//REGISTER TOPSHEET
			$topsheet['invoice_number'] = $_POST['invoice'];
			$topsheet['insurance_name'] = $data['insurance_details'][0]['name'];
			$topsheet['prepared_by'] = $_POST['prepared_by'];
			$topsheet['noted_by'] = $_POST['noted_by'];
			$topsheet['grand_total'] = $_POST['grand_total'];
			$topsheet['date'] = $_POST['date'];
			$topsheet['due_date'] = $_POST['due_date'];

			$topsheet_register = $this->claims_model->register('billings_topsheet',$topsheet);

			if($topsheet_register)
			{
				foreach($data['availments'] as $key => $details)
				{
					foreach($details as $dkey => $value)
					{
						$topsheet_details = array(
										'invoice_number' => $_POST['invoice'],
										'claims_code' => $value['code'],
										'patient_name' => $value['patient_name'],
										'hospital_name' => $value['hospital_name'],
										'company_name' => $value['company_name'],
										'total_amount' => $total_amount[$key],
										'print_date' => $data['insurance_billing'][$key][0]['print_date']
										);
						$topsheet_details_register = $this->claims_model->register('billings_topsheet_details',$topsheet_details);
					}
				}
			}

			// var_dump($data);
			$this->load->view('claims/claims_summary_print_view',$data);

			//PRINT TO PDF
			$html = $this->output->get_output();
		
			// Load library
			$this->load->library('dompdf_gen');
		
			// Convert to PDF
			$pdf = "Invoice-".$_POST['invoice'];
			$this->dompdf->load_html($html);
			$this->dompdf->render();
			$this->dompdf->stream($pdf.".pdf",array('Attachment'=>0)); //convert 
		}
	}

	function ipTopsheet()
	{
		$this->form_validation->set_rules('insurance_ip_topsheet','Insurance Name','required|trim|xss_clean');
		$this->form_validation->set_rules('ip_topsheet_date_from','Date From','required|trim|xss_clean');
		$this->form_validation->set_rules('ip_topsheet_date_to','Date To','required|trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('claims','refresh');
		}
		else
		{
			$insurance = $_POST['insurance_ip_topsheet'];
			$start = $_POST['ip_topsheet_date_from'];
			$end = $_POST['ip_topsheet_date_to'];

			$ip_topsheet = $this->claims_model->getSummarized('billings_topsheet',$insurance,'insurance_name',$start,$end);

			if($ip_topsheet)
			{
				foreach($ip_topsheet as $key => $value)
				{
					if($value['availment_type'] == 'Out-Patient')
					{
						unset($key);
					}
					else
					{
						$ip_topsheet_details = $this->claims_model->getRecordByField('billings_topsheet_details','invoice_number',$value['invoice_number']);
					}
				}
			}

			$data['ip_topsheet'] = $ip_topsheet;
			$data['ip_topsheet_details'] = $ip_topsheet_details;

			$loadedViews = array(
							'claims/claims_search_view' => NULL,
							'claims/claims_ip_topsheet_view' => $data
							);
			$this->load->template($loadedViews,$this->header_links);
		}
	}

	function reprintIPSummary($invoice_number)
	{
		$topsheet = $this->claims_model->getRecordByField('billings_topsheet','invoice_number',$invoice_number);
		$insurance = $this->claims_model->getRecordByField('insurance','name',$topsheet[0]['insurance_name']);

		foreach($topsheet as $key => $value)
		{
			$topsheet_details = $this->claims_model->getRecordByField('billings_topsheet_details','invoice_number',$value['invoice_number']);
		}

		$data['insurance'] = $insurance;
		$data['topsheet'] = $topsheet;
		$data['topsheet_details'] = $topsheet_details;

		// echo '<pre>';
		// var_dump($data);

		$this->load->view('claims/claims_summary_reprint_view',$data);

		//PRINT TO PDF
		$html = $this->output->get_output();

		//Load Library
		$this->load->library('dompdf_gen');

		//Convert to PDF
		$pdf = "Invoice-".$invoice_number;
		$this->dompdf->load_html($html);
		$this->dompdf->render();
		$this->dompdf->stream($pdf.".pdf",array('Attachment'=>0));
	}

	function opBilling()
	{
		$this->form_validation->set_rules('insurance_op_billing','Insurance Name','trim|required|xss_clean');
		$this->form_validation->set_rules('op_billing_date_from','Billing Date From','trim|required|xss_clean');
		$this->form_validation->set_rules('op_billing_date_to','Billing Date To','trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('claims','refresh');
		}
		else
		{
			$insurance = $_POST['insurance_op_billing'];
			$date_start = $_POST['op_billing_date_from'];
			$date_end = $_POST['op_billing_date_to'];

			$billed = $this->claims_model->getBilled('billings_details',$insurance,'insurance_name',$date_start,$date_end);

			if($billed)
			{
				foreach($billed as $bkey => $bvalue)
				{
					$availments[$bkey]['availments'] = $this->claims_model->getRecordByField('availments_test','code',$bvalue['loa_code']);

					foreach($availments[$bkey]['availments'] as $akey => $avalue)
					{
						$availments[$bkey]['benefits_in-out_patient'] = $this->claims_model->getRecordByField('benefits_in-out_patient','availment_id',$avalue['id']);
						$availments[$bkey]['benefits_others'] = $this->claims_model->getRecordByField('benefits_others','availment_id',$avalue['id']);
						$availments[$bkey]['benefits_others_as_charged'] = $this->claims_model->getRecordByField('benefits_others_as_charged','availment_id',$avalue['id']);
						$availments[$bkey]['diagnosis'] = $this->claims_model->getRecordByField('availments_diagnosis','code',$avalue['code']);
					}
				}
			}
			$data['availments'] = $availments;
			$data['billed'] = $billed;

			$loadedViews = array(
							'claims/claims_search_view' => NULL,
							'claims/claims_billed_op_view' => $data
							);
			$this->load->template($loadedViews,$this->header_links);
		}
	}

	function opTopsheet()
	{
		$this->form_validation->set_rules('insurance_op_topsheet','Insurance Name','required|trim|xss_clean');
		$this->form_validation->set_rules('op_topsheet_date_from','Date From','required|trim|xss_clean');
		$this->form_validation->set_rules('op_topsheet_date_to','Date To','required|trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('claims','refresh');
		}
		else
		{
			$insurance = $_POST['insurance_op_topsheet'];
			$start = $_POST['op_topsheet_date_from'];
			$end = $_POST['op_topsheet_date_to'];

			$ip_topsheet = $this->claims_model->getSummarized('billings_topsheet',$insurance,'insurance_name',$start,$end);

			if($ip_topsheet)
			{
				foreach($ip_topsheet as $key => $value)
				{
					if($value['availment_type'] == 'In-Patient' || $value['availment_type'] == 'In and Out Patient')
					{
						unset($key);
					}
					else
					{
						$ip_topsheet_details = $this->claims_model->getRecordByField('billings_topsheet_details','invoice_number',$value['invoice_number']);
					}
				}
			}

			$data['op_topsheet'] = $ip_topsheet;
			$data['op_topsheet_details'] = $ip_topsheet_details;

			$loadedViews = array(
							'claims/claims_search_view' => NULL,
							'claims/claims_op_topsheet_view' => $data
							);
			$this->load->template($loadedViews,$this->header_links);
		}
	}

	function reprintOPSummary($invoice_number)
	{
		$topsheet = $this->claims_model->getRecordByField('billings_topsheet','invoice_number',$invoice_number);
		$insurance = $this->claims_model->getRecordByField('insurance','name',$topsheet[0]['insurance_name']);

		foreach($topsheet as $key => $value)
		{
			$topsheet_details = $this->claims_model->getRecordByField('billings_topsheet_details','invoice_number',$value['invoice_number']);
		}

		$data['insurance'] = $insurance;
		$data['topsheet'] = $topsheet;
		$data['topsheet_details'] = $topsheet_details;

		// echo '<pre>';
		// var_dump($data);

		$this->load->view('claims/claims_summary_reprint_view',$data);

		//PRINT TO PDF
		$html = $this->output->get_output();

		//Load Library
		$this->load->library('dompdf_gen');

		//Convert to PDF
		$pdf = "Invoice-".$invoice_number;
		$this->dompdf->load_html($html);
		$this->dompdf->render();
		$this->dompdf->stream($pdf.".pdf",array('Attachment'=>0));
	}

	function multiBilling()
	{
		$this->form_validation->set_rules('sel_multi[]','Selection','trim|xss_clean|required');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('claims','result');
		}
		else
		{
			foreach($_POST['sel_multi'] as $key => $value)
			{
				// AVAILMENTS
				$availments[$key] = $this->claims_model->getRecordById('availments_test',$value);

				// AVAILMENTS DETAILS
				foreach($availments as $akey => $avalue)
				{
					$availments[$key]['diagnosis'] = $this->claims_model->getRecordByField('availments_diagnosis','code',$avalue[0]['code']);
				}

				// GET AMOUNT
				$benefits[$key]['benefits_in-out_patient'] = $this->claims_model->getRecordByField('benefits_in-out_patient','availment_id',$value);
				$benefits[$key]['benefits_others'] = $this->claims_model->getRecordByField('benefits_others','availment_id',$value);
				$benefits[$key]['benefits_others_as_charged'] = $this->claims_model->getRecordByField('benefits_others_as_charged','availment_id',$value);

				foreach($benefits[$key] as $bkey => $bvalue)
				{
					if($value == FALSE)
					{
						unset($bvalue);
					}
					else
					{	
						foreach($bvalue as $akey => $avalue)
						{
							if($avalue['availed_amount'] != 0.00)
							{
								$availments[$key]['amount'] = $avalue['availed_amount'];
							}
							if($avalue['availed_as-charged'] != 0.00)
							{
								$availments[$key]['amount'] = $avalue['availed_as-charged'];
							}
							@$total_amount += $availments[$key]['amount'];
						}
					}
				}
			}
			$insurance = $this->claims_model->getRecordByField('insurance','name',$availments[0][0]['insurance_name']);
			
			$data['availments'] = $availments;
			$data['insurance'] = $insurance;
			$data['total_amount'] = $total_amount;
			$data['sel_multi'] = $_POST['sel_multi'];
			// var_dump($data);

			$loadedViews = array(
						'claims/claims_bill_op_form_view' => $data
							);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function printOP()
	{

		$this->form_validation->set_rules('prepared_by','Prepared By', 'trim|xss_clean|required');
		$this->form_validation->set_rules('checked_by','Checked By', 'trim|xss_clean|required');
		$this->form_validation->set_rules('approved_by', 'Approved By', 'trim|xss_clean|required');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('claims','refresh');
		}
		else
		{
			unset($_POST['submit']);
			$data = $_POST;
			foreach($_POST['sel_multi'] as $key => $value)
			{
				// AVAILMENTS
				$availments[$key] = $this->claims_model->getRecordById('availments_test',$value);

				// AVAILMENTS DETAILS
				foreach($availments as $akey => $avalue)
				{
					$availments[$key]['diagnosis'] = $this->claims_model->getRecordByField('availments_diagnosis','code',$avalue[0]['code']);
				}

				$benefits[$key]['benefits_in-out_patient'] = $this->claims_model->getRecordByField('benefits_in-out_patient', 'availment_id',$value);
				$benefits[$key]['benefits_others'] = $this->claims_model->getRecordByField('benefits_others','availment_id',$value);
				$benefits[$key]['benefits_others_as_charged'] = $this->claims_model->getRecordByField('benefits_others_as_charged','availment_id',$value);

				foreach($benefits[$key] as $bkey => $bvalue)
				{
					if($bvalue == FALSE)
					{
						unset($bvalue);
					}
					else
					{
						foreach($bvalue as $akey => $avalue)
						{
							if($avalue['availed_amount'] != 0.00)
							{
								$availments[$key]['amount'] = $avalue['availed_amount'];
							}
							if($avalue['availed_as-charged'] != 0.00)
							{
								$availments[$key]['amount'] = $avalue['availed_as-charged'];
							}
						}
					}
				}
			}
			$insurance = $this->claims_model->getRecordByField('insurance','name',$availments[0][0]['insurance_name']);
		
			$data['availments'] = $availments;
			$data['insurance'] = $insurance;
			$data['total_amount'] = $_POST['total_amount'];
			// echo '<pre>';
			// var_dump($data);

			$this->load->view('claims/claims_bill_op_print_view',$data);

			//PRINT TO PDF
			$html = $this->output->get_output();

			//Load Library
			$this->load->library('dompdf_gen');

			//Convert to PDF
			$pdf = "Invoice-".$invoice_number;
			$this->dompdf->load_html($html);
			$this->dompdf->render();
			$this->dompdf->stream($pdf.".pdf",array('Attachment'=>0));
		}
	}
}