<?php
class accounts_model extends CI_Model {
	/**
	 * Function login
	 * 
	 * Method used to validate user's login
	 * 
	 * @access public
	 * @param String $username
	 * @param String $password
	 * @return boolean|array
	 */
	function login($username, $password) {
		$this->db->select('id, username, name, password, access, usertype');
		$this->db->from('users');
		$this->db->where('username = ' . "'" . $username . "'");
		$this->db->where('password = password(' . "'" . $password . "')");
		$this->db->limit(1);

		$query = $this->db->get();

		if($query->num_rows() == 1) {
			return $query->result();
		} else {
			return false;
		}
	}
}
?>