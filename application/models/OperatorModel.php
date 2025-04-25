<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OperatorModel extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	public function loginOperator($email, $password) {
		$operator = $this->db->get_where("operator", 
			array("Op_Email" => $email, "Status_Id" => 1))->row();
		if ($operator != null && $operator->Password == $password) {
			$user_configs = $this->getUserConfig($operator->Op_Id);
			$operator->mile_price = $user_configs->Mile_Price;
			return $operator;
		}
		return null;		
	}

	public function getOperatorById($id) {
		return $this->db->get_where("operator", array("Op_Id" => $id))->row();
	}

	public function getOperatorByEmail($email) {
		return $this->db->get_where("operator", array("Op_Email" => $email))->row();
	}
	
	public function insertOperator($op) {
		$this->db->insert("operator", $op);
		$op_id = $this->db->insert_id();
		$data = array(
			array('Op_Id' => $op_id, 'Air_Code' => 'BUE'),
			array('Op_Id' => $op_id, 'Air_Code' => 'COR'),
			array('Op_Id' => $op_id, 'Air_Code' => 'GRU'),
			array('Op_Id' => $op_id, 'Air_Code' => 'MDZ'),
			array('Op_Id' => $op_id, 'Air_Code' => 'MVD'),
			array('Op_Id' => $op_id, 'Air_Code' => 'ROS'),
			array('Op_Id' => $op_id, 'Air_Code' => 'SCL')
		);
		$this->db->insert_batch("am_user_depart", $data);				
		$cfg = new stdClass();
		$cfg->Op_Id = $op_id;
		$cfg->Mile_Price = 2.5;
		$this->db->insert("am_user_config", $cfg);
		return $this->db->get_where("operator", array("Op_Id" => $op_id))->row();
	}
	
	public function updateOperator($operator) {
		$this->db->where('Op_Email', $operator->Op_Email);
		$result = $this->db->update("operator", array(	"Op_Name" => $operator->Op_Name,
														"Rol_Id" => $operator->Rol_Id,														
														"Password" => $operator->Password,														
														"Phone" => $operator->Phone,
														"Status_Id" => $operator->Status_Id));	
		return $result == 1;
	}

	public function getUserDeparts($op_id)
	{
		$tmp = $this->db->get_where("am_user_depart", array("Op_Id" => $op_id))->result();
		$departs = array();
		foreach ($tmp as $d) {
			$departs[] = $d->Air_Code;
		}
		return $departs;
	}

	public function getUserConfig($op_id) {
		return $this->db->get_where("am_user_config", array("Op_Id" => $op_id))->row();
	}
}

?>