<?php
Class Fileuploader_model extends CI_Model {
	function insert($data) {
		return $query = $this->db->insert('uploads', $data);
	}
}
?>