<?php
/**
 * /application/core/MY_Loader.php
 *
 */
class MY_Loader extends CI_Loader {
	public function template($templates = array(), $header_data = array(), $return = FALSE) {
		// templates is an array, whereas,
		// keys  = name of view
		// value = data passed to view
		$content  = $this->view('templates/header_view', $header_data, $return);
		foreach($templates as $name => $data) {
			$content .= $this->view($name, $data, $return);
		}
		$content .= $this->view('templates/footer_view', NULL, $return);

		if ($return) {
			return $content;
		}
	}

	public function model($model, $name = '', $db_conn = FALSE) {
		if (is_array($model)) {
			foreach ($model as $file => $object_name) {
				// Linear array was passed, be backwards compatible.
				// CI already allows loading models as arrays, but does
				// not accept the model name param, just the file name
				if ( ! is_string($file)) {
					$file = $object_name;
					$object_name = NULL;
				}
				parent::model($file, $object_name);
			}
			return;
		}
		// Call the default method otherwise
		parent::model($model, $name, $db_conn);
	}
}
?>