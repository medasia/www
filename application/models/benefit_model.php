<?php
class Benefit_model extends CI_Model 
{

	function register($table, $data)
	{
		$query = $this->db->set($data);
		$query = $this->db->insert($table);
		return $query = $this->db->insert_id();
	}

	function getAllRecords($table,$field)
	{
		$query = $this->db->order_by($field);
		$query = $this->db->get($table,5000,0);

		if($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}

	function getFullRecords($table)
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

	function getRecordByID($table, $id, $select='')
	{
		$query = $this->db->select($select);
		$query = $this->db->where('id', $id);
		$query = $this->db->get($table);

		if($query->num_rows() == 1)
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}

	function getRecordByField($table,$field,$key,$select ='')
	{
		$query = $this->db->select($select);
		$query = $this->db->where($field, $key);
		$query = $this->db->get($table);

		if($query->num_rows())
		{
			return $query->result_array();
		}
		elseif($query->num_rows() == 1)
		{
			return $query->result();
		}
	}

	function getRecordByMultiField($table,$data,$select='')
	{
		$query = $this->db->select($select);
		$query = $this->db->where($data);
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

	function getLevelByID($table,$id,$field)
	{
		$query = $this->db->select($field);
		$query = $this->db->where('id',$id);
		$query = $this->db->group_by($field);
		$query = $this->db->get($table);

		// return $query->result_array();
		
		// return $result = $query->result();
		
		if($query->num_rows() == 1)
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}
	
	function getBenefits()
	{		
		// $query = $this->db->limit('100','0');
		$query = $this->db->query('SELECT * FROM basic_benefitss GROUP BY benefit_name ORDER BY benefit_type,benefit_name'); //DISTINCT id,benefit_name, details

		return $query->result();
	}

	function getBenefitsDetails($benefit_name)
	{
		$details = $this->db->select('details');
		$details = $this->db->where('benefit_name',$benefit_name);
		$details = $this->db->get('basic_benefitss');
		return $details->result_array();
	}

	function getIPRecords()
	{
		$query1= $this->db->query('SELECT DISTINCT benefit_name FROM basic_benefitss WHERE benefit_type = "IP" ORDER BY benefit_name');
		return $query1->result();
	}

	function getOPRecords()
	{
		$query2= $this->db->query('SELECT DISTINCT benefit_name FROM basic_benefitss WHERE benefit_type = "OP" ORDER BY benefit_name');
		return $query2->result();
	}

	function getIPOPRecords()
	{
		$query3 = $this->db->query('SELECT DISTINCT benefit_name FROM basic_benefitss WHERE benefit_type ="IP-OP" ORDER BY benefit_name');
		return $query3->result();
	}

	function getEditBenefit($table,$benefit_name)
	{
		$query = $this->db->where('benefit_name',$benefit_name);
		$query = $this->db->get($table);
		return $query->result();
	}

	function checkRestriction($table, $compins_id, $level, $cardholder)
	{
		$query = $this->db->where('compins_id', $compins_id);
		$query = $this->db->where('level', $level);
		$query = $this->db->where('cardholder_type', $cardholder);
		$query = $this->db->get($table);
		return $query->result();
	}

	function deleteByField($table, $field, $key)
	{
		$query = $this->db->where($field,$key);
		return $query = $this->db->delete($table);
	}

	function delete($benefit_type,$benefit_name) 
	{
		$query = "benefit_type = '$benefit_type' AND benefit_name = '$benefit_name'";
		$query = $this->db->where($query);
		return $query = $this->db->delete('basic_benefitss');
	}

	function getCountByField($table, $field, $key)
	{
		$query = $this->db->where($field, $key);
		$query = $this->db->from($table);
		return $this->db->count_all_results();
	}

	function deleteByID($table, $id)
	{
		$query = $this->db->where('id', $id);
		return $query = $this->db->delete($table);
	}

	function update($table,$field,$key,$data)
	{
		// unset($data['submit']);
		$query = $this->db->where($field,$key);
		$query = $this->db->set($data);
		return $query = $this->db->update($table);
	}
}
?>