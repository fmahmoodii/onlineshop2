<?php
class base_model extends CI_Model
{

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}
	

	public function get_data(
    $table,
    $select = '*',
    $where = NULL,
    $like = NULL,
    $or_where = NULL,
    $where_in = NULL,
    $order_by = NULL,
    $limit = NULL,
    $offset = NULL,
    $group_by = NULL,
    $join = NULL,       // جدید
    $return_type = 'object' // جدید
) {
    // انتخاب ستون‌ها
    $this->db->select($select);

    // JOINها
    if ($join != NULL && is_array($join)) {
        foreach ($join as $tbl => $cond) {
            if (is_array($cond)) {
                $this->db->join($tbl, $cond[0], isset($cond[1]) ? $cond[1] : 'left');
            } else {
                $this->db->join($tbl, $cond, 'left');
            }
        }
    }

    // WHERE شرط‌ها
    if ($where != NULL) {
        $this->db->where($where);
    }

    // LIKE
    if ($like != NULL) {
        $this->db->like($like);
    }

    // OR WHERE
    if ($or_where != NULL) {
        $this->db->or_where($or_where);
    }

    // WHERE IN
    if ($where_in != NULL && is_array($where_in)) {
        foreach ($where_in as $col => $values) {
            $this->db->where_in($col, $values);
        }
    }

    // GROUP BY
    if ($group_by != NULL) {
        $this->db->group_by($group_by);
    }

    // ORDER BY
    if ($order_by != NULL) {
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

    // نوع خروجی: object یا array
    if ($return_type == 'array') {
        return $query->result_array();
    } else {
        return $query->result_object();
    }
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




}//end model

