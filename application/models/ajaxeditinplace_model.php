<?php
Class Ajaxeditinplace_model extends CI_Model {
	/**
	 * Function update
	 * 
	 * Updates records with user inputs from different controllers and views
	 * 
	 * @access public
	 * @param String 	$table 	table to be used
	 * @param String 	$key 	the primary key
	 * @param String 	$field 	the column name
	 * @param String 	$value 	the value to be inserted
	 * @return String 			returns the string that has been updated
	 */
	function update($table, $key, $field, $value) {
		$query = $this->db->where('id', $key);
		$query = $this->db->set($field, $value);
		$query = $this->db->update($table);
		// return var_dump($this->db->queries);
		return $value;
	}

	function getAllRecords($table)
	{
		$query = $this->db->get($table);

		if($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}
}
?>