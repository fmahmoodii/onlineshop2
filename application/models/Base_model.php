<?php
class base_model extends CI_Model
{

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	

	function get_data($table, $var, $where = NULL, $like = NULL, $or_where = NULL, $where_in = NULL, $order_by = NULL, $limit = NULL, $offset = NULL, $group_by = NULL)
	{
		$this->db->select($var);

		if ($where != NULL) {
			$this->db->where($where);
		}
		if ($like != NULL) {
			$this->db->like($like);
		}
		if ($or_where != NULL) {
			$this->db->or_where($or_where);
		}
		if ($where_in != NULL) {
			$this->db->where_in($where_in);
		}
		if ($group_by != NULL) {
			$this->db->group_by($group_by);
		}
		if ($order_by != NULL) {
			$this->db->order_by($order_by);
		}

		$query = $this->db->get($table, $limit, $offset);

		return $query->result_object();

	}
	




}//end model

