<?php
Class DatabaseCreate_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->dbforge();
	}

	function createTable($table,$fields)
	{
		$this->load->dbforge();
		$query = $this->dbforge->add_field('id'); // ADD ID FIELD with primary key
		$query = $this->dbforge->add_field($fields);
		$query = $this->dbforge->add_key('patient_id');
		// $query = $this->dbforge->add_key('id',TRUE);
		// var_dump($this->dbforge);
		return $query = $this->dbforge->create_table($table);
	}

	function deleteTable($table)
	{
		return $query = $this->dbforge->drop_table($table);
	}
}
?>