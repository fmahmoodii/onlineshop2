<?php
class base_model extends CI_Model
{

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	public function get_data($table, $select = '*', $where = NULL, $like = NULL, $or_where = NULL, $where_in = NULL, $order_by = NULL, $limit = NULL, $offset = NULL, $group_by = NULL, $join = NULL, $return_type = 'object', $include_deleted = false) {
		$this->db->select($select);

		// JOIN
		if (!empty($join) && is_array($join)) {
			foreach ($join as $tbl => $cond) {
				if (is_array($cond)) {
					$this->db->join($tbl, $cond[0], $cond[1] ?? 'left');
				} else {
					$this->db->join($tbl, $cond, 'left');
				}
			}
		}

		// WHERE
		if (!empty($where)) {
			$this->db->where($where);
		}

		// حذف نرم فقط اگر include_deleted = false
		if (!$include_deleted) {
			$fields = $this->db->list_fields($table);
			if (in_array('deleted_at', $fields)) {
				$this->db->where('deleted_at', NULL);
			}
		}

		// LIKE
		if (!empty($like)) {
			$this->db->like($like);
		}

		// OR WHERE
		if (!empty($or_where)) {
			$this->db->or_where($or_where);
		}

		// WHERE IN
		if (!empty($where_in) && is_array($where_in)) {
			foreach ($where_in as $col => $values) {
				if (is_array($values) && count($values) > 0) {
					// فقط مقادیر غیر آرایه‌ای داخل آرایه رو می‌پذیریم
					$filtered_values = array_filter($values, fn($v) => !is_array($v));
					if (!empty($filtered_values)) {
						$this->db->where_in($col, $filtered_values);
					}
				}
			}
		}

		// GROUP BY
		if (!empty($group_by)) {
			$this->db->group_by($group_by);
		}

		// ORDER BY
		if (!empty($order_by)) {
			if (is_array($order_by)) {
				foreach ($order_by as $col => $dir) {
					$this->db->order_by($col, $dir);
				}
			} else {
				$this->db->order_by($order_by);
			}
		}

		// اجرای کوئری
		$query = $this->db->get($table, $limit, $offset);

		return $return_type === 'array' ? $query->result_array() : $query->result_object();
	}

	public function insert_data($table, $data, $batch = FALSE)
	{
		if (empty($table) || empty($data)) {
			return FALSE;
		}

		if ($batch === TRUE && is_array($data[0])) {
			// برای درج چند ردیف با هم
			$this->db->insert_batch($table, $data);
		} else {
			// برای درج یک ردیف
			$this->db->insert($table, $data);
		}

		// اگر درج موفق بود، برگردوندن ID یا TRUE
		if ($this->db->affected_rows() > 0) {
			return $batch ? TRUE : $this->db->insert_id();
		} else {
			return FALSE;
		}
	}

	public function update_data($table, $data, $where = NULL, $batch = FALSE, $batch_index = NULL)
	{
		if (empty($table) || empty($data)) {
			return FALSE;
		}

		// 🔹 حالت Batch Update (مثلاً چند رکورد با مقادیر متفاوت)
		if ($batch === TRUE && $batch_index != NULL) {
			$this->db->update_batch($table, $data, $batch_index);
		} else {
			// 🔹 حالت آپدیت تکی یا بر اساس شرط خاص
			if ($where != NULL) {
				$this->db->where($where);
			}
			$this->db->update($table, $data);
		}

		// ✅ بررسی موفقیت عملیات
		if ($this->db->affected_rows() >= 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function soft_delete($table, $where = NULL, $batch = FALSE, $batch_index = NULL)
	{
		if (empty($table)) {
			return FALSE;
		}

		$deletedAt = date('Y-m-d H:i:s');

		// 🔹 حالت Batch
		if ($batch === TRUE && $batch_index != NULL) {

			if (empty($where) || !is_array($where)) {
				return FALSE; // ⛔ جلوگیری از حذف کل جدول
			}

			$data = [];
			foreach ($where as $id) {
				$data[] = [
					$batch_index => $id,
					'deleted_at' => $deletedAt
				];
			}

			$this->db->update_batch($table, $data, $batch_index);

		} else {

			// 🔹 حالت تکی / شرطی
			if (empty($where) || !is_array($where)) {
				return FALSE; // ⛔ where اجباری
			}

			$this->db->where($where);
			$this->db->update($table, [
				'deleted_at' => $deletedAt
			]);
		}

		return $this->db->affected_rows() >= 0;
	}

	public function delete_data($table, $where = NULL, $where_in = NULL)
	{
		if (empty($table)) {
			return FALSE;
		}

		// 🔹 شرط معمولی
		if ($where != NULL) {
			$this->db->where($where);
		}

		// 🔹 شرط where_in برای حذف گروهی
		if ($where_in != NULL && is_array($where_in)) {
			foreach ($where_in as $column => $values) {
				$this->db->where_in($column, $values);
			}
		}

		// 🔹 اجرای حذف
		$this->db->delete($table);

		// ✅ بررسی موفقیت
		if ($this->db->affected_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function datatable($table, $columns, $post_data, $select = '*', $join = NULL, $where = NULL, $order_by_default = NULL)
	{
		// ---- آماده‌سازی Query اصلی ----
		$this->db->select($select);
		$this->db->from($table);

		// JOIN‌ها
		if ($join != NULL && is_array($join)) {
			foreach ($join as $tbl => $cond) {
				if (is_array($cond)) {
					$this->db->join($tbl, $cond[0], isset($cond[1]) ? $cond[1] : 'left');
				} else {
					$this->db->join($tbl, $cond, 'left');
				}
			}
		}

		// WHERE
		if ($where != NULL) {
			$this->db->where($where);
		}

		// اضافه کردن حذف نرم به صورت عمومی
		$fields = $this->db->list_fields($table);
		if (in_array('deleted_at', $fields)) {
			$this->db->where('deleted_at', NULL);
		}

		// ---- شمارش کل ----
		$recordsTotal = $this->db->count_all_results('', false); // false = نگه داشتن Query برای ادامه

		// ---- جستجو (Search) ----
		if (isset($post_data['search']['value']) && $post_data['search']['value'] != '') {
			$search_value = $post_data['search']['value'];
			$this->db->group_start();
			foreach ($columns as $col) {
				if ($col !== null) {
					$field = explode(' as ', $col);
					$col_name = trim($field[0]);
					$this->db->or_like($col_name, $search_value);
				}
			}
			$this->db->group_end();
		}

		// ---- شمارش بعد از فیلتر ----
		$recordsFiltered = $this->db->count_all_results('', false);

		// ---- ORDER ----
		if (isset($post_data['order'])) {
			$col_index = $post_data['order'][0]['column'];
			$dir = $post_data['order'][0]['dir'];
			if (isset($columns[$col_index]) && $columns[$col_index] !== null) {
				$field = explode(' as ', $columns[$col_index]);
				$col_name = trim($field[0]);
				$this->db->order_by($col_name, $dir);
			}
		} elseif ($order_by_default != NULL) {
			if (is_array($order_by_default)) {
				foreach ($order_by_default as $col => $dir) {
					$this->db->order_by($col, $dir);
				}
			} else {
				$this->db->order_by($order_by_default);
			}
		}

		// ---- LIMIT ----
		if (isset($post_data['length']) && $post_data['length'] != -1) {
			$this->db->limit($post_data['length'], $post_data['start']);
		}

		// ---- دریافت داده ----
		$query = $this->db->get();
		$data = $query->result();

		// ---- خروجی نهایی ----
		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	public function add_log($entity_type, $entity_id, $action, $old_value = null, $new_value = null, $details = null, $group_id = null, $operation_info = null) {
		$user_id = $this->session->userdata('id') ?? null;
		$ip_address = $this->input->ip_address();

		$data = [
			'entity_type'    => $entity_type,
			'entity_id'      => $entity_id,
			'user_id'        => $user_id,
			'action'         => $action,
			'old_value'      => is_array($old_value) ? json_encode($old_value, JSON_UNESCAPED_UNICODE) : $old_value,
			'new_value'      => is_array($new_value) ? json_encode($new_value, JSON_UNESCAPED_UNICODE) : $new_value,
			'ip_address'     => $ip_address,
			'timestamp'      => date('Y-m-d H:i:s'),
			'group_id'       => $group_id,
			'details'        => $details,
			'operation_info' => $operation_info
		];

		try {
			return $this->db->insert('logs', $data);
		} catch (Exception $e) {
			log_message('error', 'Add Log Error: ' . $e->getMessage());
			return false;
		}
	}




	public function has_permission($user_id, $permissions, $table_name = null)
	{
		if (!is_array($permissions)) {
			$permissions = [$permissions];
		}

		// گرفتن roleهای کاربر
		$roles = $this->db
			->select('role_id')
			->from('user_roles')
			->where('user_id', $user_id)
			->where('isActive', 1)
			->get()
			->result();

		if (!$roles) return false;

		$role_ids = array_map(function($r){ return $r->role_id; }, $roles);

		// گرفتن همه پرمیشن‌های کاربر
		$user_permissions = $this->db
			->select('p.key_name, p.table_name')
			->from('permissions p')
			->join('role_permissions rp', 'rp.permission_id = p.id AND rp.isActive = 1', 'inner')
			->where_in('rp.role_id', $role_ids)
			->where('p.isActive', 1)
			->get()
			->result();

		// بررسی حداقل یکی از پرمیشن‌ها (OR)
		foreach($user_permissions as $p){
			if ($table_name) {
				if ($p->table_name == $table_name && in_array($p->key_name, $permissions)) {
					return true;
				}
			} else {
				if (in_array($p->key_name, $permissions)) {
					return true;
				}
			}
		}

		return false;
	}











	public function get_user_permissions($user_id)
	{
		$this->db->select('p.name')
			->from('user_roles ur')
			->join('role_permissions rp', 'ur.role_id = rp.role_id')
			->join('permissions p', 'rp.permission_id = p.id')
			->where([
				'ur.user_id' => $user_id,
				'ur.isActive' => 1,
				'rp.isActive' => 1,
				'p.isActive' => 1
			]);
		$result = $this->db->get()->result_array();
		return array_column($result, 'name');
	}





}//end model

