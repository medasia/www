<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Uphist extends CI_Controller
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
				// case 'sysad':
				// break;

				// default:
				// 	echo '<script>alert("You are not allowed to access this portion of the site!!!!");</script>';
				// 	redirect('','refresh');
			}
		}
		else
		{
			//If no session, redirect to login page
			redirect('../', 'refresh');
		}
	}
	function index() {
		if($this->session->userdata('logged_in'))
		{
			$session_data = $this->session->userdata('logged_in');

			switch($session_data['usertype'])
			{
				case 'sysad':
					$data['files'] = $this->records_model->getAllRecords('uploads');
					foreach($data['files'] as $key => $value)
					{
						$data['files'][$key]['actions'] = anchor(base_url().'records/uphist/download/'.$value['id'], 'Download');
					}
					$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/uphist/uphist_view' => $data
							);
					$this->load->template($loadedViews, $this->header_links);
				break;

				default;
					echo '<script>alert("You are not allowed to access this portion of the site!");</script>';
					redirect('','refresh');
			}
		}
	}
	
	function download($id) {
		$filedata = $this->records_model->getRecordById('uploads', $id);
		$file = $filedata[0]['path'].$filedata[0]['hash'].'.'.end(explode(".", $filedata[0]['filename']));
		$mime = get_mime_by_extension($file);
		file_download($filedata[0]['filename'], $mime, $file);
	}

	function downloadTemp($id) {
		$filedata = $this->records_model->getRecordById('templates', $id);
		$file = $filedata[0]['path'].$filedata[0]['hash'].'.'.end(explode(".", $filedata[0]['filename']));
		$mime = get_mime_by_extension($file);
		file_download($filedata[0]['filename'], $mime, $file);
	}

	function view_pdf($uploaded_to,$filename)
	{
		$filename = urldecode($filename);
		$filedata = $this->records_model->getRecordByMultiField('uploads',
														array('uploaded_to'=>$uploaded_to,
															'filename'=>$filename));
		$file = $filedata[0]['path'].$filedata[0]['hash'].'.'.end(explode('.', $filedata[0]['filename']));
		$mime = get_mime_by_extension($file);
		echo '<head><title>View '.$filename.'</title></head>'; 
		$this->output
           ->set_content_type($mime)
           ->set_output(file_get_contents($filedata[0]['path'].$filedata[0]['hash'].'.pdf'));
	}
}
?>