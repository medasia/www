<?php
Class Benefits_model extends CI_Model {
	/**
	 * Function getAllRecords
	 * 
	 * Fetches all records on table given
	 * 
	 *@ access public
	 * @param String 	$table 	table to be used
	 * @return boolean|array
	 */
	function getAllRecords($table) {
		$query = $this->db->get($table);

		if($query->num_rows()) {
			return $query->result_array();
		} else {
			return false;
		}
	}
	/**
	 * Function getRecordById
	 * 
	 * Fetches a single(or multiple, depending on DB records) row using the column ID
	 * 
	 * @access public
	 * @param String 	$table 	table to be used
	 * @param Int 		$id 	ID of record to be used
	 * @return boolean|array
	 */
	function getRecordById($table, $id) {
		$query = $this->db->where('id', $id);
		$query = $this->db->get($table);

		if($query->num_rows() == 1) {
			return $query->result();
		} else {
			return false;
		}
	}
	/**
	 * Function register
	 * 
	 * Inserts a record to DB
	 * 
	 * @access public
	 * @param String 	$table 	table to be used
	 * @param Array 	$data 	data to be inserted
	 * @return array
	 */
	function register($table, $data) {
		$query = $this->db->set($data);
		$query = $this->db->set('password', "PASSWORD('".$data['password']."')", FALSE);
		return $query = $this->db->insert($table); 
	}
	/**
	 * Function update
	 * 
	 * Updates (a) record/s on DB using ID
	 * 
	 * @access public
	 * @param String 	$table 	table to be used
	 * @param Int 		$id 	ID of record to be used
	 * @param Array 	$data 	data to be inserted
	 * @return array
	 */
	function update($table, $id, $data) {
		unset($data['submit']);
		$query = $this->db->where('id', $id);
		$query = $this->db->set($data);
		if(isset($data['password'])) $query = $this->db->set('password', "PASSWORD('".$data['password']."')", FALSE);
		return $query = $this->db->update($table);
	}
	/**
	 * Function delete
	 * 
	 * Deletes a record on DB using ID
	 * 
	 * @access public
	 * @param String 	$table 	table to be used
	 * @param Int 		$id 	ID to be used on WHERE clause
	 */
	function delete($table, $id) {
		$query = $this->db->where('id', $id);
		return $query = $this->db->delete($table);
	}
}
?>