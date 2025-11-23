<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('base_model');
		$this->load->library('jalali_date'); // اجباری برای اطمینان از لود صحیح
	}

	//<<--------------- date_shamsi_ghamari ---------------->>
	public function date_j($miladi_date)
	{
		if (empty($miladi_date)) return null;

		$exploadeddate = explode(' ', $miladi_date);
		$gmtdate = explode('-', $exploadeddate[0]);
		return $this->jalali_date->gregorian_to_jalali($gmtdate[0], $gmtdate[1], $gmtdate[2], '-');
	}
	//<<--------------- end date_shamsi_ghamari ---------------->>


	// 📊 صفحه‌ی اصلی ادمین (داشبورد)
	public function index()
	{
		// بررسی لاگین بودن
		if (!$this->session->userdata('id')) {
			redirect('admin/login_page');
			return;
		}

		$data['title'] = 'داشبورد';
		$data['products'] = $this->base_model->get_data('products', '*');

		// داده‌های نمودار (نمونه‌ی ساده)
		$query1 = $this->db->query("SELECT COUNT(id_p) AS count FROM shopping_cart_order GROUP BY YEAR(date) ORDER BY date");
		$data['click'] = json_encode(array_column($query1->result(), 'count'), JSON_NUMERIC_CHECK);

		// (در صورت نیاز این رو با داده‌ی متفاوت جایگزین کن)
		$query2 = $this->db->query("SELECT COUNT(id_p) AS count FROM shopping_cart_order GROUP BY YEAR(date) ORDER BY date");
		$data['viewer'] = json_encode(array_column($query2->result(), 'count'), JSON_NUMERIC_CHECK);

		$this->load->view('admin/admin-panel', $data);
	}

	// 🪪 صفحه‌ی ورود
	public function login_page()
	{
		if ($this->session->userdata('id')) {
			redirect('admin');
			return;
		}

		$data['title'] = 'ورود';
		$this->load->view('admin/layout/header2', $data);
		$this->load->view('admin/login');
	}

	// 🔐 لاگین
	public function login()
	{
		if ($this->input->post()) {
			$username = $this->input->post('user_name', true);
			$password = $this->input->post('password', true);

			$admin = $this->base_model->get_data('admin', '*', [
				'user_name' => $username,
				'password'  => $password
			]);

			$group_id = uniqid('grp_', true);
			$operationInfo = "ورود ادمین";

			if (!empty($admin)) {
				$this->session->set_userdata('id', $admin[0]->id);

				$this->base_model->add_log(
					'admin',
					$admin[0]->id,
					'login_success',
					null,
					null,
					'ورود موفق به پنل ادمین با نام کاربری: ' . $username,
					$group_id,
					$operationInfo
				);

				redirect('admin');
			} else {
				$this->base_model->add_log(
					'admin',
					null,
					'login_failed',
					null,
					null,
					'تلاش ناموفق برای ورود به پنل ادمین با نام کاربری: ' . $username,
					$group_id,
					$operationInfo
				);

				$this->session->set_flashdata('err', 'نام کاربری یا رمز عبور اشتباه است.');
				redirect('admin/login_page');
			}
		} else {
			redirect('admin/login_page');
		}
	}

	// 🚪 خروج از حساب
	public function logout()
	{
		$user_id = isset($this->session->userdata['id']) ? $this->session->userdata['id'] : null;

		if ($user_id) {
			// گرفتن یوزرنیم
			$admin = $this->base_model->get_data('admin', 'user_name', ['id' => $user_id]);
			$username = isset($admin[0]) ? $admin[0]->user_name : null;

			$group_id = uniqid('grp_', true);

			$this->base_model->add_log(
				'admin',                        // entity_type
				$user_id,                        // entity_id
				'logout',                        // action
				null,                             // old_value
				null,                             // new_value
				"خروج موفق از پنل ادمین با نام کاربری: $username", // details
				$group_id,                        // group_id
				'خروج ادمین',                     // operation_info
				null,
				null,
				null
			);
		}

		$this->session->sess_destroy();
		redirect('admin/login_page');
	}

	public function registered_users(){
		$data['profile']=$this->base_model->get_data('profile','*');
		$data['register']=$this->base_model->get_data('register','*');
		$data['title']='کاربران';
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/registered-users');
	}

	public function users_list()
	{
		$columns = [
			null,                   // برای checkbox یا دکمه‌ها
			'role.name',
			'profile.name',
			'profile.family',
			'register.phone_number',
			'register.created',
			'register.modified',
			null, null, null, null
		];

		$join = [
			'profile' => 'register.id = profile.user_id',
			'role' => 'register.role = role.id'
		];

		$table = 'register';
		$select = 'profile.id, profile.user_id, register.id as user_id, role.name as role, profile.name, profile.family, register.phone_number, register.created, register.modified, register.isActive';

		// دریافت داده‌ها با مدل جدید
		$result = $this->base_model->datatable($table, $columns, $_POST, $select, $join, null, ['register.id' => 'DESC']);

		$data = [];
		foreach($result['data'] as $row) {
			$sub_array = [];
			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" user_id="'.htmlspecialchars($row->user_id).'" id_prof="'.htmlspecialchars($row->id).'">';
			$sub_array[] = htmlspecialchars($row->role);
			$sub_array[] = htmlspecialchars($row->name);
			$sub_array[] = htmlspecialchars($row->family);
			$sub_array[] = htmlspecialchars($row->phone_number);
			$sub_array[] = htmlspecialchars($row->created);
			$sub_array[] = htmlspecialchars($row->modified);

			$sub_array[] = ($row->isActive == 0)
				? '<button type="button" id="active" user_id="'.$row->user_id.'" id_prof="'.$row->id.'" class="btn btn-primary btn-xs">فعالسازی</button>'
				: '<button type="button" id="deactive" user_id="'.$row->user_id.'" id_prof="'.$row->id.'" class="btn btn-secondry btn-xs">غیرفعالسازی</button>';

			$sub_array[] = '<button type="button" id="reset" user_id="'.$row->user_id.'" id_prof="'.$row->id.'" class="btn btn-info"><i class="fa fa-key"></i></button>';
			$sub_array[] = '<a href="'.base_url('admin/edit_user/'.$row->user_id).'"><button class="btn btn-warning"><i class="fa fa-edit"></i></button></a>';
			$sub_array[] = '<button id="delete" user_id="'.$row->user_id.'" id_prof="'.$row->id.'" class="btn btn-danger"><i class="fa fa-trash"></i></button>';

			$data[] = $sub_array;
		}

		// خروجی JSON برای DataTables
		$output = [
			"draw" => intval($_POST["draw"]),
			"recordsTotal" => $result['recordsTotal'],
			"recordsFiltered" => $result['recordsFiltered'],
			"data" => $data
		];

		echo json_encode($output);
	}

	public function delete_user()
	{
		if ($_POST && isset($_POST['user_ids']) && is_array($_POST['user_ids'])) {

			$user_ids = $_POST['user_ids'];
			$group_id = uniqid('grp_', true);
			$operationInfo = "حذف کاربران";

			// گرفتن اطلاعات قبل از حذف از جدول register
			$register_users = $this->base_model->get_data(
				'register',
				'*',
				null,
				null,
				null,
				['id' => $user_ids]
			);

			// گرفتن اطلاعات قبل از حذف از جدول profile
			$profiles = $this->base_model->get_data(
				'profile',
				'*',
				null,
				null,
				null,
				['user_id' => $user_ids]
			);

			// ساخت map برای دسترسی سریع به نام و نام خانوادگی با user_id
			$profileMap = [];
			foreach ($profiles as $p) {
				$profileMap[$p->user_id] = $p;  // هر user_id → رکورد profile
			}

			// حذف گروهی
			$this->db->where_in('user_id', $user_ids)->delete('profile');
			$this->db->where_in('id', $user_ids)->delete('register');

			// ثبت لاگ برای register + اضافه کردن name و family (در صورت وجود)
			foreach ($register_users as $user) {

				$fullName = isset($profileMap[$user->id])
					? $profileMap[$user->id]->name . ' ' . $profileMap[$user->id]->family
					: 'نامشخص';

				$this->base_model->add_log(
					'register',
					$user->id,
					'delete',
					(array)$user,
					null,
					'حذف کاربر: ' . $fullName,
					$group_id,
					$operationInfo
				);
			}

			// ثبت لاگ برای profile
			foreach ($profiles as $p) {
				$this->base_model->add_log(
					'profile',
					$p->id,
					'delete',
					(array)$p,
					null,
					'حذف کاربر: ' . $p->name . ' ' . $p->family,
					$group_id,
					$operationInfo
				);
			}

			echo 1;
		}
	}

	function toggle_user_status()
	{
		if ($_POST) {

			$user_ids = [];

			// چند انتخابی
			if ($_POST['user_ids'] && is_array($_POST['user_ids'])) {
				$user_ids = $_POST['user_ids'];
			}

			// تکی
			if ($_POST['user_id']) {
				$user_ids[] = $_POST['user_id'];
			}

			if (!empty($user_ids)) {

				$status = isset($_POST['status']) ? intval($_POST['status']) : 1;

				$group_id = uniqid('grp_', true);
				$operationInfo = "تغییر وضعیت کاربران";

				// گرفتن اطلاعات قبل از تغییر
				$users_before = $this->base_model->get_data('register', '*', NULL, NULL, NULL, ['id' => $user_ids]);

				foreach ($users_before as $user) {

					// old_value فقط همان فیلد تغییر یافته
					$old_value = [
						'isActive' => $user->isActive
					];

					// آپدیت
					$this->base_model->update_data('register', ['isActive' => $status], ['id' => $user->id]);

					// مقدار جدید
					$new_value = [
						'isActive' => $status
					];

					// توضیحات لاگ
					$details =
						($status ? 'فعالسازی ' : 'غیرفعالسازی ') .
						'کاربر با شماره موبایل: ' . $user->phone_number;

					// ثبت در لاگ
					$this->base_model->add_log(
						'register',
						$user->id,
						'update_status',
						$old_value,
						$new_value,
						$details,
						$group_id,
						$operationInfo
					);
				}

				echo 1;
			} else {
				echo 0;
			}
		} else {
			echo 0;
		}
	}

	public function reset_pass()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$new_pass = $_POST['new_pass'];
			$re_pass = $_POST['re_new_pass'];

			if ($new_pass === $re_pass) {
				// گرفتن اطلاعات قبلی کاربر
				$user_before = $this->base_model->get_data('register', '*', ['id' => $id]);
				if (!$user_before || !isset($user_before[0])) {
					echo 0; // کاربر پیدا نشد
					return;
				}

				$user_before = (array) $user_before[0]; // برای ذخیره در old_value
				$old_value = $user_before;

				// هش کردن رمز جدید
				$hashed_pass = password_hash($new_pass, PASSWORD_BCRYPT);

				// آپدیت رمز در جدول register
				$this->base_model->update_data('register', ['password' => $hashed_pass], ['id' => $id]);

				// گرفتن اطلاعات جدید بعد از آپدیت
				$user_after = $this->base_model->get_data('register', '*', ['id' => $id]);
				$user_after = isset($user_after[0]) ? (array) $user_after[0] : [];

				// آماده‌سازی داده‌های لاگ
				$group_id = uniqid('grp_', true);
				$operationInfo = "تغییر رمز عبور کاربر";

				// ثبت لاگ
				$this->base_model->add_log(
					'register',                         // entity_type
					$id,                                // entity_id
					'update_password',                  // action
					$old_value,                         // old_value = اطلاعات قبلی کامل
					$user_after,                        // new_value = اطلاعات جدید کامل
					'تغییر رمز عبور برای کاربر با شماره موبایل: ' . $user_before['phone_number'], // details
					$group_id,                          // group_id
					$operationInfo                      // operation_info
				);

				echo 1; // موفق
			} else {
				echo 0; // رمزها مطابقت ندارند
			}
		} else {
			echo 0; // درخواست POST نیست
		}
	}

	public function _phoneRegex($phn_num){
		// اگر فیلد خالی بود، اجازه بده rule "required" پیام خودش را نمایش دهد
		if (empty($phone)) {
			return TRUE;
		}
		if (preg_match('/^(\+98|0)?9\d{9}$/', $phn_num)){
			return true;
		}else{
			return false;
		}
	}
	public function _phoneExists($phone)
	{
		// اگر خالی بود (به خاطر required)، اجازه بده ولیدیشن ادامه پیدا کند
		if (empty($phone)) {
			return TRUE;
		}

		$existing = $this->base_model->get_data('register', '*', ['phone_number' => $phone]);

		if (!empty($existing)) {
//			$this->form_validation->set_message('_phoneExists', 'این شماره موبایل قبلاً ثبت شده است');
			return FALSE;
		}

		return TRUE;
	}
	public function _phoneRegex2($phn_num2)
    {
        if (empty($phn_num2)) {
            // خالی بودن مجازه
            return TRUE;
        }
        if (preg_match('/^(\+98|0)?9\d{9}$/', $phn_num2)){
            return TRUE;
        }
        $this->form_validation->set_message('postal_check', 'شماره موبایل نادرست است');
        return FALSE;
    }
	public function _postal_check($str)
	{
		if (empty($str)) {
			// خالی بودن مجازه
			return TRUE;
		}
		if (preg_match('/^\d{10}$/', $str)) {
			return TRUE;
		}
		$this->form_validation->set_message('postal_check', 'کد پستی باید 10 رقم باشد.');
		return FALSE;
	}
	public function get_city(){
		$province_id=$this->input->post('province_id');
		$city=$this->base_model->get_data('city','*',array('province_id'=>$province_id));
		$result='<option value="">انتخاب کنید</option>';
		foreach($city as $row){
			$result=$result."<option value='$row->id'>$row->name</option>";
		}
		echo $result;
	}
	function search_ostan()
	{
		$output = '';
		$query = '';
		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}

		$data = $this->base_model->search_ostan($query);


		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
			{
				$output .= ' <a
				class="prov_tag" id="prov_tag_'.$row->id.'" id_prov="'.$row->id.'" style="display: block;">'.$row->name.'</a> ';

			}
		} else {
			$output = 0;
		}
		echo $output;
	}


    public function check_phone()
    {
        if ($this->input->post('phone_number')) {
            $phone = $this->input->post('phone_number', true);

            // بررسی وجود شماره موبایل در جدول register
            $existing = $this->base_model->get_data('register', '*', ['phone_number' => $phone]);

            if (!empty($existing)) {
                echo 'exists';
            } else {
                echo 'ok';
            }
        } else {
            echo 'ok';
        }
    }


    public function insert_user()
	{
		$data['profile']=$this->base_model->get_data('profile','*');
		$data['register']=$this->base_model->get_data('register','*');
		$data['role']=$this->base_model->get_data('role','*');
		$data['province']=$this->base_model->get_data('province','*');
		$data['city']=$this->base_model->get_data('city','*');

		$data['title']='افزودن کاربر';
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/insert_user');
	}

	public function add_user()
	{
		if ($this->input->post()) {
			$this->load->library('form_validation');
			$this->load->helper('form');

			$phone_number = $this->input->post('phone_number', true);

			// پیام‌ها و قوانین ولیدیشن
			$this->form_validation->set_message('required', 'فیلد الزامی است');
			$this->form_validation->set_message('min_length', '%s باید حداقل %d کاراکتر داشته باشد');
			$this->form_validation->set_message('max_length', '%s باید حداکثر %d کاراکتر داشته باشد');
			$this->form_validation->set_message('regex_match', 'فقط از حروف استفاده کنید');
			$this->form_validation->set_message('_phoneRegex', 'شماره وارد شده نادرست است');
			$this->form_validation->set_message('_phoneExists', 'شماره وارد شده تکراری است');
			$this->form_validation->set_message('_phoneRegex2', 'شماره وارد شده نادرست است');
			$this->form_validation->set_message('_postal_check', 'در صورت ورود کدپستی، باید 10 رقم باشد');

			$this->form_validation->set_rules('role', 'نوع کاربر', 'required');
			$this->form_validation->set_rules('password', 'رمز عبور', 'required|min_length[8]|max_length[25]');
			$this->form_validation->set_rules('phone_number', 'شماره موبایل', 'required|min_length[10]|max_length[11]|callback__phoneRegex|callback__phoneExists');
			$this->form_validation->set_rules('phone_number1', 'شماره موبایل ضروری', 'callback__phoneRegex2');
			$this->form_validation->set_rules('postal_code', 'کد پستی', 'callback__postal_check');

			if ($this->form_validation->run()) {

				$group_id = uniqid('grp_', true);
				$operationInfo = "افزودن کاربر جدید";

				// بررسی وجود کاربر قبلی
				$existing_user = $this->base_model->get_data('register', '*', ['phone_number' => $phone_number]);
				if (!empty($existing_user)) {
					$this->base_model->add_log(
						'user',
						$existing_user[0]->id,
						'add_user_failed',
						null,
						null,
						'تلاش ناموفق برای افزودن کاربر با شماره موبایل: ' . $phone_number,
						$group_id,
						$operationInfo
					);

					$this->session->set_flashdata('err', 'کاربری با این شماره موبایل وجود دارد.');
					redirect('admin/insert_user');
					return;
				}

				// داده‌های جدول register
				$data_register = [
					'created' => $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s'),
					'role' => $this->input->post('role', true),
					'phone_number' => $phone_number,
					'password'      => password_hash($this->input->post('password', true), PASSWORD_DEFAULT)
				];

				// درج داده با insert_data
				$user_id = $this->base_model->insert_data('register', $data_register);

				// داده‌های جدول profile
				$data_profile = [
					'user_id' => $user_id,
					'created' => $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s'),
					'name' => $this->input->post('name', true),
					'family' => $this->input->post('family', true),
					'phone_number' => $phone_number,
					'reciever_phone_number' => $this->input->post('phone_number1', true),
					'ostan' => $this->input->post('ostan', true),
					'city' => $this->input->post('city', true),
					'address' => $this->input->post('address', true),
					'postal_code' => $this->input->post('postal_code', true)
				];

				$this->base_model->insert_data('profile', $data_profile);

				// ثبت لاگ برای جدول register
				$this->base_model->add_log(
					'register',
					$user_id,
					'add_user_success',
					null,
					(array)$data_register,
					'افزودن کاربر جدید با شماره موبایل: ' . $phone_number,
					$group_id,
					$operationInfo
				);

				// ثبت لاگ برای جدول profile
				$this->base_model->add_log(
					'profile',
					$user_id,
					'add_user_success',
					null,
					(array)$data_profile,
					'افزودن کاربر جدید با شماره موبایل: ' . $phone_number,
					$group_id,
					$operationInfo
				);

				$this->session->set_flashdata('success', 'ok');
				redirect('admin/insert_user');

			} else {
				$this->insert_user();
			}
		}
	}

	public function edit_user($id)
	{
		$data['title']='ویرایش کاربر';
		$data['profile']=$this->base_model->get_data('profile','*',array('user_id'=>$id));
		$data['role']=$this->base_model->get_data('role','*');
		$data['province']=$this->base_model->get_data('province','*');
		$data['city']=$this->base_model->get_data('city','*');
		$data['register']=$this->base_model-> get_data('register','*',array('id'=>$id));


		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/edit-user');
	}

	public function edit_u($id)
	{
		if ($this->input->post()) {
			$this->load->library('form_validation');
			$this->load->helper('form');

		// پیام‌ها و قوانین ولیدیشن
		$this->form_validation->set_message('required', 'فیلد الزامی است');
		$this->form_validation->set_message('min_length', '%s باید حداقل %d کاراکتر داشته باشد');
		$this->form_validation->set_message('max_length', '%s باید حداکثر %d کاراکتر داشته باشد');
		$this->form_validation->set_message('regex_match', 'فقط از حروف استفاده کنید');
		$this->form_validation->set_message('_phoneRegex', 'شماره وارد شده نادرست است');
//		$this->form_validation->set_message('_phoneExists', 'شماره وارد شده تکراری است');
		$this->form_validation->set_message('_phoneRegex2', 'شماره وارد شده نادرست است');
		$this->form_validation->set_message('_postal_check', 'در صورت ورود کدپستی، باید 10 رقم باشد');

		$this->form_validation->set_rules('role', 'نوع کاربر', 'required');
		$this->form_validation->set_rules('password', 'رمز عبور', 'required|min_length[8]|max_length[25]');
//		$this->form_validation->set_rules('phone_number', 'شماره موبایل', 'required|min_length[10]|max_length[11]|callback__phoneRegex|callback__phoneExists');
		$this->form_validation->set_rules('phone_number1', 'شماره موبایل ضروری', 'callback__phoneRegex2');
		$this->form_validation->set_rules('postal_code', 'کد پستی', 'callback__postal_check');


			if (!$this->form_validation->run()) {

			date_default_timezone_set("Asia/Tehran");
			$modified_time = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');

			// --- new data ---
			$new_profile = [
				'name' => $this->input->post('name'),
				'family' => $this->input->post('family'),
				'reciever_phone_number' => $this->input->post('phone_number1'),
				'ostan' => $this->input->post('ostan'),
				'city' => $this->input->post('city'),
				'address' => $this->input->post('address'),
				'postal_code' => $this->input->post('postal_code'),
				'modified' => $modified_time
			];

			$new_register = [
				'role' => $this->input->post('role'),
				'modified' => $modified_time
			];

			// --- old data ---
			$old_profile = (array)$this->base_model->get_data('profile', '*', ['user_id' => $id])[0];
			$old_register = (array)$this->base_model->get_data('register', '*', ['id' => $id])[0];

			// --- diffs ---
			$diff_p_old = $diff_p_new = [];
			foreach ($new_profile as $k => $v) {
				if ((string)$old_profile[$k] !== (string)$v) {
					$diff_p_old[$k] = $old_profile[$k];
					$diff_p_new[$k] = $v;
				}
			}

			$diff_r_old = $diff_r_new = [];
			foreach ($new_register as $k => $v) {
				if ((string)$old_register[$k] !== (string)$v) {
					$diff_r_old[$k] = $old_register[$k];
					$diff_r_new[$k] = $v;
				}
			}

			// --- update DB ---
			$this->base_model->update_data('profile', $new_profile, ['user_id' => $id]);
			$this->base_model->update_data('register', $new_register, ['id' => $id]);

			$group_id = uniqid('grp_', true);
			$operationInfo = "ویرایش اطلاعات کاربر";

				$phone_number = $this->base_model->get_data('register', 'phone_number', ['id' => $id])[0]->phone_number;

			// --- logs ---
			if (!empty($diff_r_old)) {
				$this->base_model->add_log(
					'register',
					$id,
					'update',
					$diff_r_old,
					$diff_r_new,
					' ویرایش کاربر با شماره موبایل: ' . $phone_number,
					$group_id,
					$operationInfo
				);
			}

			if (!empty($diff_p_old)) {
				$this->base_model->add_log(
					'profile',
					$id,
					'update',
					$diff_p_old,
					$diff_p_new,
					' ویرایش کاربر با شماره موبایل: ' . $phone_number,
					$group_id,
					$operationInfo
				);
			}

			redirect('admin/edit_user/'.$id);

			}
		}
	}











	public function delete_users_checked() {
		if (isset($_POST['ids1']) && isset($_POST['ids2'])) {
			$ids1 = explode(',', $_POST['ids1']);
			$ids2 = explode(',', $_POST['ids2']);

			$results = $this->base_model->delete_rows_by_ids($ids1,'register');
			$results2 = $this->base_model->delete_rows_by_ids($ids2,'profile');

			if ($results === TRUE && $results2 === TRUE) {
				echo '<span style="color:green;">row(s) successfully deleted</span>';
			} else {
				echo '<span style="color:red;">Something went wrong during row deletion</span>';
			}
		} else {
			echo '<span style="color:red;">You must select at least one row for deletion</span>';
		}
	}















	public function category_test20()
	{
		$data['title']='دسته بندی محصولات';
		for($i='1';$i<='10';$i++){
			$data['category'.$i]=$this->base_model->get_data('category_test','*');
		}
//		$data['category1']=$this->base_model->get_data('category_test','*');
//		$data['category2']=$this->base_model->get_data('category_test','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/cat-test3');

	}
//	public function getCategoryTree($level = 0, $prefix = '') {
//		$rows = $this->db
//			->select('id,parentId,name_cat')
//			->where('parentId', $level)
//			->order_by('id','asc')
//			->get('category_test')
//			->result();
//
//		$category = '';
//		if (count($rows) > 0) {
//			foreach ($rows as $row) {
//				$category .= $prefix . $row->name_cat . "\n";
//				// Append subcategories
//				$category .= $this->getCategoryTree($row->id, $prefix . '-');
//			}
//		}
//		return $category;
//	}
	public function getCategoryTree($level = 0, $prefix = 2) {
		$rows = $this->db
			->select('id,parentId,name_cat')
			->where('parentId', $level)
			->order_by('id','asc')
			->get('category_test')
			->result();

		$category = '';

		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$id = $row->id;
				$name_cat = $row->name_cat;
				$category .=
				'<div id="box_'.$id.'" id_cat="'.$id.'">
				<div style="margin-right: '.$prefix.'px;position: relative;border: 0.5px solid #ccc;padding: 10px"t="'.$id.'">
				<i style="display: none; " id="plus_'.$id.'" class="fa fa-plus plus" id_cat="'.$id.'"></i>
				<i style="display: inline; " id="minus_'.$id.'" class="fa fa-minus minus" id_cat="'.$id.'"></i>
				<span class="" style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" id_cat="'.$id.'">'.$name_cat.'</span>
				<div style="display: inline; margin-right: 20px;padding: 5px;background-color: #fcecec">
					<button class="btn btn-default btn-sm" type="button" id="add" id_cat="'.$id.'" >
						<i class="fa fa-plus"></i>
					</button>
					<button class="btn btn-default btn-sm" type="button" id="edit" id_cat="'.$id.'" >
						<i class="fa fa-edit"></i>
					</button>
					<button class="btn btn-default btn-sm" type="button" id="delete" id_cat="'.$id.'" >
						<i class="fa fa-trash"></i>
					</button>
				</div>
				<br>
			</div>
			</div>
			
			';
					// Append subcategories
				$category .= $this->getCategoryTree($row->id, 20+$prefix);
			}
		}
		return $category;
	}

	public function cat_minus($parentId) {
		$rows = $this->db
			->select('id,parentId,name_cat')
			->where('parentId', $parentId)
			->order_by('id','asc')
			->get('category_test')
			->result();

		$data = '';

		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$data .= $row->id.",". $this->cat_minus($row->id);
			}
		}

		echo $data;
	}

//	public function category_test2()
//	{
//
//		$data['title']='دسته بندی محصولات';
//		$data['cat']= $this->getCategoryTree();
//
//		$this->load->view('admin/layout/header',$data);
//		$this->load->view('admin/layout/sidebar');
//		$this->load->view('admin/cat-test3');
//
//	}
	public function category_test2()
	{
		$data['title']='دسته بندی محصولات';
		$flat = $this->base_model->get_categories_with_user();
		$data['categories'] = $this->base_model->build_tree($flat);
		$this->load->view('admin/layout/header',$data);

		$this->load->view('admin/cat-test4');
	}
	public function getCategories() {
		$query = $this->db->get('category_test');
		$categories = $query->result_array();

		// ساختار درختی با level
		$tree = $this->buildTree($categories);
		echo json_encode(['data' => $tree]);
	}

	private function buildTree($categories, $parentId = 0, $level = 0) {
		$branch = [];
		foreach ($categories as $category) {
			if ($category['parentId'] == $parentId) {
				$category['level'] = $level;
				$children = $this->buildTree($categories, $category['id'], $level + 1);
				if ($children) {
					$category['children'] = $children;
				}
				$branch[] = $category;
			}
		}
		return $branch;
	}

	public function category_test()
	{

		$data['title']='دسته بندی محصولات';
		for($i='1';$i<='10';$i++){
			$data['category'.$i]=$this->base_model->get_data('category_test','*');
		}


//		$data['category1']=$this->base_model->get_data('category_test','*');
//		$data['category2']=$this->base_model->get_data('category_test','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/cat-test');

	}
	public function insert_cat(){
		if ($this->input->post()) {
			date_default_timezone_set("Asia/Tehran");

			$data = [
				'created'   => $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s'),
				'modified'   => $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s'),
				'isActive'  => 1,
				'name_cat'  => $this->input->post('name_cat'),
				'parentId'  => $this->input->post('parentId')
			];

			$insert_id = $this->base_model->insert('category_test', $data);

			if ($insert_id) {
				$data['id'] = $insert_id;

				echo json_encode(['success' => true, 'data' => $data]);
			} else {
				echo json_encode(['success' => false]);
			}
		}
	}




//	public function insert_cat(){
//		if ($_POST){
//			date_default_timezone_set("Asia/Tehran");
//			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
//			$data['isActive']='1';
//			$data['name_cat']=$_POST['name_cat'];
//			$name_cat=$_POST['name_cat'];
//			$data['parentId']=$_POST['parentId'];
//			$mar=$_POST['mar'];
////			$parentId=$_POST['parentId'];
////			$data['level']=$_POST['level'];
////			$level=$_POST['level'];
////			$details=$level+1;
////			$f=($level-1)*25;
//
//			$id = $this->base_model->insert('category_test', $data);
//
//			$output = '';
//			$output .=
//					'<div id="box_'.$id.'" id_cat="'.$id.'">
//					<div style="margin-right: '.$mar.'px;position: relative;border: 0.5px solid #ccc;padding: 10px" id_cat="'.$id.'">
//				<i style="display: none; " id="plus_'.$id.'" class="fa fa-plus plus" id_cat="'.$id.'"></i>
//				<i style="display: inline; " id="minus_'.$id.'" class="fa fa-minus minus" id_cat="'.$id.'"></i>
//				<span class="" style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" id_cat="'.$id.'">'.$name_cat.'</span>
//				<div style="display: inline; margin-right: 20px;padding: 5px;background-color: #fcecec">
//					<button class="btn btn-default btn-sm" type="button" id="add" id_cat="'.$id.'" >
//						<i class="fa fa-plus"></i>
//					</button>
//					<button class="btn btn-default btn-sm" type="button" id="edit" id_cat="'.$id.'" >
//						<i class="fa fa-edit"></i>
//					</button>
//					<button class="btn btn-default btn-sm" type="button" id="delete" id_cat="'.$id.'" >
//						<i class="fa fa-trash"></i>
//					</button>
//				</div>
//				<br>
//			</div>
//			</div>
//			';
//
//
//
//
//			echo $output;
//		}
//	}
	public function delete_category()
	{
		$this->output->set_content_type('application/json');


		$id = (int) $this->input->post('id');
		if (!$id) {
			echo json_encode(['success' => false, 'message' => 'invalid id']);
			return;
		}

		// --- حذف دسته و فرزندانِ مستقیم (طبق کدی که داشتی) ---
		$this->base_model->delete_row('category_test', 'id', $id);
		$this->base_model->delete_row('category_test', 'parentId', $id);

		// آپدیت محصولات (همون منطق خودت)
		$dataUpdate = ['id_cat1' => ''];
		$this->base_model->update('products', ['id_cat1' => $id], $dataUpdate);
		$products = $this->base_model->get_data('products','id_cat2',['id_cat1'=>$id]);
		foreach ($products as $p){
			$this->base_model->update('products', ['id_cat2' => $p->id_cat2], $dataUpdate);
		}

		echo json_encode(['success' => true]);
	}


//	public function delete_category(){
//		if ($_POST) {
//			$id = $_POST['id'];
//			$this->base_model->delete_row('category_test', 'id', $id);
//			$this->base_model->delete_row('category_test', 'parentId', $id);
//
//			$data['id_cat1'] = '';
//			$this->base_model->update('products', array('id_cat1' => $id), $data);
//			$products=$this->base_model->get_data('products','id_cat2',array('id_cat1'=>$id));
//			foreach ($products as $p){
//				$id_cat2 = $p->id_cat2;
//				$this->base_model->update('products', array('id_cat2' => $id_cat2), $data);
//			}
//
//		}
//	}
	function search()
	{
		$query = '';

		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}
		$data = $this->base_model->search($query);


		$ids = array();

		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
			{
				$ids[] = $row->id;
			}
		}

		echo json_encode($ids);
	}



	public function category()
	{
		if(isset($_SESSION['id'])){

			$data['title']='دسته بندی محصولات';
			$data['category1']=$this->base_model->get_data('category1','*');
			$data['category2']=$this->base_model->get_data('category2','*');
			$this->load->view('admin/layout/header',$data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/category');
		}
	}
	public function category_list(){
		$fetch_data = $this->base_model->category_make_datatables();
		$data = array();

		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" id_cat1="'.$row->id_cat1.'" id_cat2="'.$row->id_cat2.'"></input> ';
			$sub_array[] = $row->name_cat1;
			$sub_array[] = $row->name_cat2;
			$sub_array[] = '
			<button type="button" id="copy" id_cat1="'.$row->id_cat1.'" id_cat2="'.$row->id_cat2.'" class="btn btn-info btn-xs">کپی</button>
			';
			$sub_array[] = '
			<button type="button" id="edit" id_cat1="'.$row->id_cat1.'" id_cat2="'.$row->id_cat2.'" class="btn btn-warning btn-xs">ویرایش</button>
			';
			$sub_array[] = '<button type="button" id="delete" id_cat1="'.$row->id_cat1.'" id_cat2="'.$row->id_cat2.'" class="btn btn-danger btn-xs">حذف</button>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->category_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->category_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}
	public function insert_category()
	{
		if(isset($_SESSION['id'])){

			$data['title']='دسته بندی جدید';
			$data['category1']=$this->base_model->get_data('category1','*');
			$data['category2']=$this->base_model->get_data('category2','*');
			$this->load->view('admin/layout/header',$data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/insert_category');
		}
	}
	public function insert_cat1()
	{
		if ($_POST){
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['isActive']='1';
			$data['name_cat1']=$_POST['name_cat1'];
			$id=$this->base_model->insert('category1', $data);
			$category1=$this->base_model->get_data('category1','*',array('id'=>$id));
			echo json_encode($category1);
		}
	}
	public function insert_cat2()
	{
		if ($_POST){
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['isActive']='1';
			$data['id_cat1']=$_POST['id_cat1'];
			$data['name_cat2']=$_POST['name_cat2'];
			$id=$this->base_model->insert('category2', $data);
			$category2=$this->base_model->get_data('category2','*',array('id'=>$id));
			echo json_encode($category2);
		}
	}
	public function get_category2(){
		$id_cat1=$this->input->post('id_cat1');
		$Category2=$this->base_model->get_data('category2','*',array('id_cat1'=>$id_cat1));
		$result='<option value="">انتخاب کنید</option>';
		foreach($Category2 as $row){
			$result=$result."<option value='$row->id'>$row->name_cat2</option>";
		}
		echo $result;
	}

	public function category1()
	{
		if(isset($_SESSION['id'])){

			$data['title']='سطح اول';
			$data['category1']=$this->base_model->get_data('category1','*');
			$data['category2']=$this->base_model->get_data('category2','*');
			$this->load->view('admin/layout/header',$data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/category1');
		}
	}
	public function category1_list(){
		$fetch_data = $this->base_model->category1_make_datatables();
		$data = array();

		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" value="'.$row->id.'" id_cat1="'.$row->id.'"></input> ';
			$sub_array[] = '<span id="name_' . $row->id . '">'.$row->name_cat1.'</span>';
			if($row->IsActive==0) {$sub_array[] = '<span style="color: red;font-size: 12px;">غیرفعال</span>';}
			else if($row->IsActive==1){$sub_array[] = '<span style="color: green;font-size: 12px;">فعال</span>';}
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->IsActive==0) {$sub_array[] = '<button type="button" id="active" id_cat1="'.$row->id.'"  class="btn btn-primary btn-xs">فعال سازی</button>';}
			else if($row->IsActive==1){$sub_array[] = '<button type="button" id="deactive" id_cat1="'.$row->id.'"  class="btn btn-secondary btn-xs">غیرفعال سازی</button>';}
			$sub_array[] = '
			<button type="button" id="edit" id_cat1="'.$row->id.'" class="btn btn-warning">
			<i class="fa fa-edit"></i>
</button>
			';
			$sub_array[] = '<button type="button" id="delete" id_cat1="'.$row->id.'" class="btn btn-danger">
<i class="fa fa-trash"></i>
</button>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->category1_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->category1_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}
	public function edit_cat1(){
		if ($_POST) {
			$id = $_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name_cat1'] = $_POST['name_cat1'];
			$this->base_model->update('category1', array('id' => $id), $data);
			echo 1;
		}
	}
	public function cat2_product(){
		$id = $_POST['id'];
		$i=0;
		$j=0;
		$products=$this->base_model->get_data('products','*',array('id_cat1'=>$id));
		$category2=$this->base_model->get_data('category2','*',array('id_cat1'=>$id));
		if(isset($products[0])){foreach ($products as $p){
			$data['prd'] = ++$i;
		}}else{
			$data['prd'] = 0;
		}
		if(isset($category2[0])){foreach ($category2 as $cat2){
			$data['cat2'] = ++$j;
		}}else{
		$data['cat2'] = 0;
		}
		echo json_encode($data);
	}
	public function cat2_product_2(){
		if (isset($_POST['ids'])) {
			$id = explode(',', $_POST['ids']);
			$products = $this->base_model->get_datas('products', '*', 'id_cat1', $id);
			$category2 = $this->base_model->get_datas('category2', '*', 'id_cat1', $id);
		}
		$i = 0;
		$j = 0;
		if(isset($products[0])){foreach ($products as $p){
			$data['prd'] = ++$i;
		}}else{
			$data['prd'] = 0;
		}
		if(isset($category2[0])){foreach ($category2 as $cat2){
			$data['cat2'] = ++$j;
		}}else{
			$data['cat2'] = 0;
		}
		echo json_encode($data);
	}
	public function delete_cat1()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$this->base_model->delete_row('category1', 'id', $id);
			$this->base_model->delete_row('category2', 'id_cat1', $id);
			$data['id_cat1'] = '';
			$this->base_model->update('products', array('id_cat1' => $id), $data);
			$products=$this->base_model->get_data('products','id_cat2',array('id_cat1'=>$id));
			foreach ($products as $p){
				$id_cat2 = $p->id_cat2;
				$this->base_model->update('products', array('id_cat2' => $id_cat2), $data);
			}

//			$this->base_model->delete_row('products', 'id_cat1', $id);
//			$products=$this->base_model->get_data('products','*',array('id_cat1'=>$id));
//			$this->base_model->delete_row('images', 'user_id', $products->code);
			echo 1;
		}
	}
	public function delete_cats1_checked() {
		if (isset($_POST['ids'])) {
			$ids = explode(',', $_POST['ids']);

			$results1 = $this->base_model->delete_rows_by_ids($ids,'category1');
			$results2 = $this->base_model->delete_rows_by_col('id_cat1', $ids, 'category2');

			$data['id_cat1'] = '';
			$this->base_model->update_rows_by_col('id_cat1',$ids,'products', $data);
			$results3 = $products=$this->base_model->get_data('products','id_cat2',array('id_cat1'=>$id));
			foreach ($products as $p){
				$id_cat2 = $p->id_cat2;
				$results4 = $this->base_model->update('products', array('id_cat2' => $id_cat2), $data);
			}

			if ($results1 === TRUE && $results2 === TRUE && $results3 === TRUE && $results4 === TRUE) {
				echo '<span style="color:green;">row(s) successfully deleted</span>';
			} else {
				echo '<span style="color:red;">Something went wrong during row deletion</span>';
			}
		} else {
			echo '<span style="color:red;">You must select at least one row for deletion</span>';
		}
	}
	public function edit_cats1_checked(){

		if ($_POST) {
			$ids = explode(',', $_POST['id']);

			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			if($_POST['name']!=''){
				$data['name_cat1'] = $_POST['name'];}
			$this->base_model->update_rows_by_col('id',$ids,'category1',$data);


		}

	}

	public function category2()
	{
		if(isset($_SESSION['id'])){

			$data['title']='سطح دوم';
			$data['category1']=$this->base_model->get_data('category1','*');
			$data['category2']=$this->base_model->get_data('category2','*');
			$this->load->view('admin/layout/header',$data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/category2');
		}
	}
	public function category2_list(){
		$fetch_data = $this->base_model->category2_make_datatables();
		$data = array();

		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" value="'.$row->id.'" id_cat2="'.$row->id.'"></input> ';
			$sub_array[] = '<span id="name_' . $row->id . '">'.$row->name_cat2.'</span>';
			if($row->IsActive==0) {$sub_array[] = '<span style="color: red;font-size: 12px;">غیرفعال</span>';}
			else if($row->IsActive==1){$sub_array[] = '<span style="color: green;font-size: 12px;">فعال</span>';}
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->IsActive==0) {$sub_array[] = '<button type="button" id="active" id_cat2="'.$row->id.'"  class="btn btn-primary btn-xs">فعال سازی</button>';}
			else if($row->IsActive==1){$sub_array[] = '<button type="button" id="deactive" id_cat2="'.$row->id.'"  class="btn btn-secondary btn-xs">غیرفعال سازی</button>';}
			$sub_array[] = '
			<button type="button" id="edit"  id_cat2="'.$row->id.'" class="btn btn-warning">
			<i class="fa fa-edit"></i>
</button>
			';
			$sub_array[] = '<button type="button" id="delete"  id_cat2="'.$row->id.'" class="btn btn-danger">
<i class="fa fa-trash"></i>
</button>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->category2_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->category2_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}
	public function edit_cat2(){
		if ($_POST) {
			$id = $_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name_cat2'] = $_POST['name_cat2'];
			$this->base_model->update('category2', array('id' => $id), $data);
			echo 1;
		}
	}
	public function delete_cat2()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$data['id_cat2'] = $_POST[''];
			$this->base_model->delete_row('category2', 'id', $id);
			$this->base_model->update('products', array('id_cat2' => $id), $data);
			echo 1;
		}
	}
	public function delete_cats2_checked() {
		if (isset($_POST['ids'])) {
			$ids = explode(',', $_POST['ids']);

			$results1 = $this->base_model->delete_rows_by_ids($ids,'category2');
//			$results2 = $this->base_model->delete_rows_by_col('id_cat2', $ids, 'category3');

//			$data['id_cat2'] = '';
//			$this->base_model->update_rows_by_col('id_cat2',$ids,'products', $data);
//			$results3 = $products=$this->base_model->get_data('products','id_cat3',array('id_cat2'=>$id));
//			foreach ($products as $p){
//				$id_cat3 = $p->id_cat3;
//				$results4 = $this->base_model->update('products', array('id_cat3' => $id_cat2), $data);
//			}

			if ($results1 === TRUE) {
				echo '<span style="color:green;">row(s) successfully deleted</span>';
			} else {
				echo '<span style="color:red;">Something went wrong during row deletion</span>';
			}
		} else {
			echo '<span style="color:red;">You must select at least one row for deletion</span>';
		}
	}
	public function edit_cats2_checked(){

		if ($_POST) {
			$ids = explode(',', $_POST['id']);

			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			if($_POST['name']!=''){
				$data['name_cat2'] = $_POST['name'];}
			$this->base_model->update_rows_by_col('id',$ids,'category2',$data);


		}

	}

	function add_attr()
	{

		$model1 = $_POST['model'];
		$brand1 = $_POST['brand'];
		$size1 = $_POST['size'];
		$jens1 = $_POST['jens'];
		$color1 = $_POST['color'];
		$price = $_POST['price'];
		$buy_price = $_POST['buy_price'];
		$off_percent = $_POST['off_percent'];
		$supply = $_POST['supply'];
		$isActive = $_POST['isActive'];
		$default_row = $_POST['default_row'];


		$model=$this->base_model->get_data('model','*');
		$jens=$this->base_model->get_data('jens','*');
		$brand=$this->base_model->get_data('brand','*');
		$color=$this->base_model->get_data('color','*');
		$size=$this->base_model->get_data('size','*');

		foreach ($model as $mo){if($mo->id==$model1){
			$model2=$mo->name;
		}if($model1==''){$model2="";}}

		foreach ($jens as $je){if($je->id==$jens1){
			$jens2=$je->name;
		}if($jens1==''){$jens2="";}}

		foreach ($size as $si){if($si->id==$size1){
			$size2=$si->name;
		}if($size1==''){$size2="";}}

		foreach ($brand as $br){if($br->id==$brand1){
			$brand2=$br->name;
		}if($brand1==''){$brand2="";}}

		foreach ($color as $co){if($co->id==$color1){
			$color2=$co->color_code;
			$color3=$co->name;
		}if($color1==''){$color2="";$color3="";}}


		$output = '';
		$output .= '
					
					<tr id="shown_tr">
						<td class="hide_div"> 
						<button id="del_row" class="btn btn-danger del_item_btn" type="button">
							<i class="fa fa-minus"></i>
						</button>
						</td>
						<td class="model_cells" style="display: none">' . $model2 . '</td>
						<td class="jens_cells" style="display: none">' . $jens2 . '</td>
						<td class="size_cells" style="display: none">' . $size2 . '</td>
						<td class="brand_cells" style="display: none">' . $brand2 . '</td>
						<td class="color_cells" style="display: none;">
						<span '. ($color2 != '' ? 'style="height: 16px;width: 16px;border:1px solid black;border-radius: 50%;display: inline-block;background:' .$color2. ';"' : '') .' ></span>
						<span>'. ($color3 != '' ? $color3 : '') .'</span>
						</td>
						<td>' . $buy_price . '</td>
						<td>' . $price . '</td>
						<td>' . ( $off_percent=='' ? '' : $off_percent."%") . '</td>
						<td>' . $supply . '</td>
						<td id="isActive_hide" style="display: none">' . $isActive . '</td>
						<td ><input id="isActive2" type="checkbox" checked></td>
						<td id="default_hide" style="display: none">' . $default_row . '</td>
						<td ><input id="default2" name="default" type="radio" '. ($default_row == '1' ? 'checked' : '') .' ></td>
					</tr>
					<tr id="hidden_tr" style="display: none">
						<td class="hide_div"> 
						<button id="del_row" class="btn btn-danger del_item_btn" type="button">
							<i class="fa fa-minus"></i>
						</button>
						</td>
						<td class="model_cells" style="display: none">' . $model1 . '</td>
						<td class="jens_cells" style="display: none">' . $jens1 . '</td>
						<td class="size_cells" style="display: none">' . $size1 . '</td>
						<td class="brand_cells" style="display: none">' . $brand1 . '</td>
						<td class="color_cells" style="display: none">' . $color1 . '</td>
						<td>' . $buy_price . '</td>
						<td>' . $price . '</td>
						<td>' . $off_percent . '</td>
						<td>' . $supply . '</td>
						<td id="isActive_hide" style="display: none">' . $isActive . '</td>
						<td ><input id="isActive" type="checkbox" checked></td>
						<td id="default_hide" style="display: none">' . $default_row . '</td>
						<td ><input id="default" name="" type="radio" '. ($default_row == '1' ? 'checked' : '') .' ></td>
					
					</tr>
		  ';




		echo $output;
	}
	public function default_attr(){
		$code = $_POST['code'];
		$id = $_POST['id'];
		$data['default'] = $_POST['default'];
		$data['isActive'] = $_POST['isActive'];
		$data2['default'] = 0;

		$this->base_model->update('product_attributes', array('id' => $id), $data);
		$this->base_model->update('product_attributes', array('id!=' => $id,'code_p' => $code), $data2);


	}
	public function isActive_attr(){
		$code = $_POST['code'];
		$id = $_POST['id'];

		$data['isActive'] = $_POST['isActive'];

		$this->base_model->update('product_attributes', array('id' => $id,'code_p' => $code), $data);
		echo 1;

	}
	public  function insert_prd8(){
		$dataa['code_p']= $_POST['code'];
		$data = json_decode($this->input->post('sendData'));
		foreach ($data as $row){
			date_default_timezone_set("Asia/Tehran");
			$dataa['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$dataa['model']=$row->model;
			$dataa['jens']=$row->jens;
			$dataa['size']=$row->size;
			$dataa['brand']=$row->brand;
			$dataa['color']=$row->color;
			$dataa['buy_price']=$row->buy_price;
			$dataa['price']=$row->price;
			$dataa['off_percent']=$row->off_percent;
			$dataa['supply']=$row->supply;
			$dataa['isActive']=$row->isActive_hide;
			$dataa['default']=$row->default_hide;

			$this->base_model->insert('product_attributes', $dataa);
		}
		echo 1;



	}


	public function add_attr_editProducts(){
		if ($_POST) {
			$data['code_p'] = $_POST['code'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['model'] = $_POST['model'];
			$data['jens'] = $_POST['jens'];
			$data['size'] = $_POST['size'];
			$data['brand'] = $_POST['brand'];
			$data['color'] = $_POST['color'];
			$data['price'] = $_POST['price'];
			$data['off_percent'] = $_POST['off_percent'];
			$data['supply'] = $_POST['supply'];
			$data['isActive'] = '1';
			$data['default'] = '0';

			$this->base_model->insert('product_attributes', $data);

		}
		$prd_attr=$this->base_model->get_data('product_attributes','id',
			array(
				'code_p' => $_POST['code'],
				'modified' => $data['modified'],
				'model' => $_POST['model'],
				'jens'=>$_POST['jens'],
				'size'=>$_POST['size'],
				'brand'=>$_POST['brand'],
				'color'=>$_POST['color'],
				'price'=>$_POST['price'],
				'off_percent'=>$_POST['off_percent'],
				'supply' => $_POST['supply'],
				)
		);
		foreach ($prd_attr as $x){
			$data['id']=$x->id;
		}


		$i=0;
		$product_attributes=$this->base_model->get_data('product_attributes','default',array('id' => $data['id'],'code_p' => $_POST['code']));
		foreach ($product_attributes as $pa){
			if($pa->default == 1){
				$i++;
			}
		}
		$data['i']=$i;

		echo json_encode($data);

	}
	public function edit_attr_editProducts(){
		if($_POST){
			$id=$_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['model'] = $_POST['model'];
			$data['jens'] = $_POST['jens'];
			$data['size'] = $_POST['size'];
			$data['brand'] = $_POST['brand'];
			$data['color'] = $_POST['color'];
 			$data['price'] = $_POST['price'];
			$data['off_percent'] = $_POST['off_percent'];
			$data['supply'] = $_POST['supply'];

			$data3=$this->base_model->get_data('shopping_cart','*', array('id_attr' => $id));

			$p=$_POST['price'];
			$op=$_POST['off_percent'];

			if($_POST['off_percent']==null){
				$pri=$p;
			}else{
				$pri=$p-($op/100)*$p;
			}
			foreach ($data3 as $row){
				$qty=$row->qty;
				$data2['price'] = $p;
				$data2['off_percent'] = $op;
				$data2['off_price'] = ($p-$pri)*$qty;
				$data2['total_price1'] = $qty*$p;
				$data2['total_price'] = $qty*$pri;
				$data2['final_price'] = $qty*$pri;

			}

			if($this->base_model->is_input_available($id, 'id_attr', 'shopping_cart')){
				$this->base_model->update('shopping_cart', array('id_attr' => $id), $data2);
				echo 2;
			}else{
				echo 3;
			}

			$this->base_model->update('product_attributes',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function hhh(){
		$model=$this->base_model->get_data('model','*');
		$jens=$this->base_model->get_data('jens','*');
		$brand=$this->base_model->get_data('brand','*');
		$color=$this->base_model->get_data('color','*');
		$size=$this->base_model->get_data('size','*');

		foreach ($model as $mo){if($mo->id==$_POST['model']){
			$data2['model']=$mo->name;
		}if($_POST['model']==''){$data2['model']="";}}

		foreach ($jens as $je){if($je->id==$_POST['jens']){
			$data2['jens']=$je->name;
		}if($_POST['jens']==''){$data2['jens']="";}}

		foreach ($size as $si){if($si->id==$_POST['size']){
			$data2['size']=$si->name;
		}if($_POST['size']==''){$data2['size']="";}}

		foreach ($brand as $br){if($br->id==$_POST['brand']){
			$data2['brand']=$br->name;
		}if($_POST['brand']==''){$data2['brand']="";}}

		foreach ($color as $co){if($co->id==$_POST['color']){
			$data2['color']=$co->name;
		}if($_POST['color']==''){$data2['color']="";}}

		$data2['price']=$_POST['price'];
		$data2['isActive']='1';



		echo json_encode($data2);
	}

	public function del_product_attr(){
		if ($_POST) {
			$id = $_POST['id'];
			$code = $_POST['code'];
			$this->base_model->delete_row('product_attributes', 'id', $id);
			$this->base_model->delete_row('shopping_cart', 'id_attr', $id);
			$i=0;
			$product_attributes=$this->base_model->get_data('product_attributes','default',array('code_p' => $code));
			foreach ($product_attributes as $pa){
				if($pa->default == 1){
					$i++;
				}
			}
			echo $i;
		}
	}


	public function insert_product()
	{
		$data['title']='محصول گذاری';
		$data['category_test']=$this->base_model->get_data('category_test','*');
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['model']=$this->base_model->get_data('model','*');
		$data['size']=$this->base_model->get_data('size','*');
		$data['jens']=$this->base_model->get_data('jens','*');
		$data['brand']=$this->base_model->get_data('brand','*');
		$data['color']=$this->base_model->get_data('color','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/insert-product3');
	}
	public function insert_product2()
	{
		$data['title']='محصول گذاری';
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['size']=$this->base_model->get_data('size','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/insert-product2');
	}


	public function insert_prd()
	{
		if ($_POST) {
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['code'] = $_POST['code'];
			$data['id_cat1']=$_POST['id_cat1'];
			$data['id_cat2']=$_POST['id_cat2'];
			$data['name'] = $_POST['name'];
			$data['text'] = $_POST['text'];
			$data['isActive'] = 1;
			$this->base_model->insert('products', $data);
			echo 1;
		}
	}


//size
	public function size(){
		$data['title']='جدول سایز';
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/size');
	}
	function size_list(){
		$fetch_data = $this->base_model->size_make_datatables();
		$data = array();

		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = "";
			$sub_array[] = '
			<a href="'. base_url('admin/info_size/').$row->id.'">
			<span id="name_' . $row->id . '">'.$row->name.'</span>
			</a>';
			if($row->IsActive==0) {$sub_array[] = '<span style="color: red;font-size: 12px;">غیرفعال</span>';}
			else if($row->IsActive==1){$sub_array[] = '<span style="color: green;font-size: 12px;">فعال</span>';}
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			 if($row->IsActive==0) {$sub_array[] = '<button type="button" id="active" id_size="'.$row->id.'" class="btn btn-primary btn-xs">فعال</button>';}
			else if($row->IsActive==1){$sub_array[] = '<button type="button" id="deactive" id_size="'.$row->id.'" class="btn btn-primary btn-xs">غیرفعال</button>';}
//			$sub_array[] = '
//			<button type="button" id="copy" id_size="'.$row->id.'" class="btn btn-info btn-xs">کپی</button>
//			';
			$sub_array[] = '
			<button type="button" id="edit" id_size="'.$row->id.'" class="btn btn-warning btn-xs">ویرایش</button>
			';
			$sub_array[] = '<button type="button" id="delete" id_size="'.$row->id.'" class="btn btn-danger btn-xs">حذف</button>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->size_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->size_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}
	public function info_size($id){
		$data['title']='محصولات مرتبط';

		$this->session->set_flashdata('id_size', $id);

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/info_size');
	}
	public function info_size_list(){
		$fetch_data = $this->base_model->size_attr_make_datatables();
		$data = array();
		$id = $this->session->flashdata('id_size');
		foreach($fetch_data as $row)
		{if ($row->size==$id){

			$sub_array = array();
			$sub_array[] = '';
			$sub_array[] = '<a href="'. base_url('admin/edit_product/').$row->code.'">
			<span>'.$row->name.'</span>
			</a>';
			$sub_array[] = $row->supply;
			$sub_array[] = $row->price;
			$sub_array[] = $row->off_percent;
			$sub_array[] = '';


			$data[] = $sub_array;
		}}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->size_attr_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->size_attr_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);

	}
	public function insert_size()
	{
		if ($_POST) {
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$data['IsActive'] = 1;
			$this->base_model->insert('size', $data);
			echo 1;
		}

	}
	public function get_size($id)
	{
		$size=$this->base_model->get_data('size','name',array('id'=>$id));
		if (isset($size[0])){
			echo $size[0]->name;
		}
//		foreach ($size as $si){
//			echo $si->name;
//			break;
//		}

	}
	function search_size()
	{
		$output = '';
		$query = '';
		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}

		$data = $this->base_model->search_size($query);


		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
			{
				$output .= ' <a
				class="tag" id="tag_'.$row->id.'" id_c="'.$row->id.'" style="display: block;">'.$row->name.'</a> ';

			}
		} else {
			$output = 0;
		}
		echo $output;
	}
	function size_validation(){
		echo $this->base_model->is_input_available('$_POST["name"]','name','size');
		$data['name'] = $_POST['name'];
		$this->form_validation->set_rules('name', 'size', 'required|trim|is_unique[size.name]');

		if($this->form_validation->run())
		{
			echo 1;
		}else{
			if($this->base_model->is_input_available($_POST["name"],'name','size') || $_POST["name"]==''){
				echo 2;
			}

		}
	}
	function check_size()
	{

		if($this->base_model->is_input_available($_POST["name"],'name','size'))
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}
		else
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}


	}
	function check_size_in_tables()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$product_attr=$this->base_model->get_data('product_attributes','id',array('size'=>$id));

			$i2=0;
			foreach ($product_attr as $p) {
				if ($this->base_model->is_input_available($id, 'size', 'product_attributes')) {
					$i2++;
				}
			}
			if($i2>0){
				echo ' تعداد ' .$i2. ' محصول با این سایز ویرایش می شود'. "\r\n";
			}

//			$i3=0;
//			foreach ($products as $p) {
//				if ($this->base_model->is_input_available($p->id, 'id_p', 'shopping_cart')) {
//					$i3++;
//				}
//			}
//			if($i3>0){
//				echo ' تعداد ' .$i2. ' محصول با این سایز از سبدهای خرید حذف می شود';
//			}

		}


	}
	public function edit_si(){
		if($_POST){
			$id=$_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$this->base_model->update('size',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function delete_size()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$product_attr=$this->base_model->get_data('product_attributes','*',array('size'=>$id));
			if($this->base_model->is_input_available($id, 'id', 'size')){
//				$this->base_model->delete_row('size', 'id', $id);
				$this->base_model->delete_rows_by_ids($id,'size');
//				$results3 = $this->base_model->delete_rows_by_col('code_p',$codes,'product_attributes');
			}
			if($this->base_model->is_input_available($id, 'size', 'product_attributes')){
//				$this->base_model->delete_row('product_attributes', 'size', $id);
				$data['size'] = 0;
				$this->base_model->update('product_attributes',array('size'=>$id) ,$data);
				echo 2;
			}
//			foreach ($product_attr as $p_attr) {
//				if($this->base_model->is_input_available($p_attr->id, 'id_attr', 'shopping_cart')){
//					$data['size'] = 0;
//					$this->base_model->update('shopping_cart',array('size'=>$id) ,$data);
////					$this->base_model->delete_row('shopping_cart', 'id_attr', $p_attr->id);
//					echo 3;
//				}
//			}

			echo 1;
		}
	}
	public function size_copy(){
		$id=$_POST['id'];
		date_default_timezone_set("Asia/Tehran");
		$name=$this->base_model->get_data('size','name',array('id'=>$id));
		if(isset($name[0])){
			echo $name1=$name[0]->name;
		}
//		foreach ($name as $na){
//			echo $name1=$na->name;
//			break;
//		}

//		$characters = '01';
//		$charactersLength = strlen($characters);
//		$randomString = '';
//		for ($i = 0; $i < 2; $i++) {
//			$randomString .= $characters[random_int(0, $charactersLength - 1)];
//		}
//		$result2=$name1.'-'.$randomString;

		for ($j = 1; $j < 1000; $j++) {
			$result=$name1.'-copy('.$j.')';
			if($this->base_model->is_size_unique($result)){
				$data['name'] = $result;
				$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');

				$IsActive=$this->base_model->get_data('size','IsActive',array('id'=>$id));
				if(isset($IsActive[0])){
					echo $data['IsActive']=$IsActive[0]->IsActive;
				}
//				foreach ($IsActive as $cr){
//					echo $data['IsActive']=$cr->IsActive;
//					break;
//				}

				$this->base_model->insert('size', $data);
				break;
			}
			else{
				echo 'error';
			}
		}

	}
//size - end


//model
	public function model(){
		$data['title']='جدول مدل';
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/model');
	}
	function model_list(){
		$fetch_data = $this->base_model->model_make_datatables();
		$data = array();

		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = "";
			$sub_array[] = '
			<a href="'. base_url('admin/info_model/').$row->id.'">
			<span id="name_' . $row->id . '">'.$row->name.'</span>
			</a>';
			if($row->IsActive==0) {$sub_array[] = '<span style="color: red;font-model: 12px;">غیرفعال</span>';}
			else if($row->IsActive==1){$sub_array[] = '<span style="color: green;font-model: 12px;">فعال</span>';}
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->IsActive==0) {$sub_array[] = '<button type="button" id="active" id_model="'.$row->id.'" class="btn btn-primary btn-xs">فعال</button>';}
			else if($row->IsActive==1){$sub_array[] = '<button type="button" id="deactive" id_model="'.$row->id.'" class="btn btn-primary btn-xs">غیرفعال</button>';}
//			$sub_array[] = '
//			<button type="button" id="copy" id_model="'.$row->id.'" class="btn btn-info btn-xs">کپی</button>
//			';
			$sub_array[] = '
			<button type="button" id="edit" id_model="'.$row->id.'" class="btn btn-warning btn-xs">ویرایش</button>
			';
			$sub_array[] = '<button type="button" id="delete" id_model="'.$row->id.'" class="btn btn-danger btn-xs">حذف</button>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->model_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->model_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}
	public function info_model($id){
		$data['title']='محصولات مرتبط';

		$this->session->set_flashdata('id_model', $id);

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/info_model');
	}
	public function info_model_list(){
		$fetch_data = $this->base_model->model_attr_make_datatables();
		$data = array();
		$id = $this->session->flashdata('id_model');
		foreach($fetch_data as $row)
		{if ($row->model==$id){

			$sub_array = array();
			$sub_array[] = '';
			$sub_array[] = '<a href="'. base_url('admin/edit_product/').$row->code.'">
			<span>'.$row->name.'</span>
			</a>';
			$sub_array[] = $row->supply;
			$sub_array[] = $row->price;
			$sub_array[] = $row->off_percent;
			$sub_array[] = '';


			$data[] = $sub_array;
		}}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->model_attr_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->model_attr_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);

	}
	public function insert_model()
	{
		if ($_POST) {
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$data['IsActive'] = 1;
			$this->base_model->insert('model', $data);
			echo 1;
		}

	}
	public function get_model($id)
	{
		$model=$this->base_model->get_data('model','name',array('id'=>$id));
		if (isset($model[0])){
			echo $model[0]->name;
		}


	}
	function search_model()
	{
		$output = '';
		$query = '';
		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}

		$data = $this->base_model->search_model($query);


		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
			{
				$output .= ' <a
				class="tag" id="tag_'.$row->id.'" id_c="'.$row->id.'" style="display: block;">'.$row->name.'</a> ';

			}
		} else {
			$output = 0;
		}
		echo $output;
	}
	function model_validation(){
		echo $this->base_model->is_input_available('$_POST["name"]','name','model');
		$data['name'] = $_POST['name'];
		$this->form_validation->set_rules('name', 'model', 'required|trim|is_unique[model.name]');

		if($this->form_validation->run())
		{
			echo 1;
		}else{
			if($this->base_model->is_input_available($_POST["name"],'name','model') || $_POST["name"]==''){
				echo 2;
			}

		}
	}
	function check_model()
	{

		if($this->base_model->is_input_available($_POST["name"],'name','model'))
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}
		else
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}


	}
	function check_model_in_tables()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$product_attr=$this->base_model->get_data('product_attributes','id',array('model'=>$id));

			$i2=0;
			foreach ($product_attr as $p) {
				if ($this->base_model->is_input_available($id, 'model', 'product_attributes')) {
					$i2++;
				}
			}
			if($i2>0){
				echo ' تعداد ' .$i2. ' محصول با این سایز ویرایش می شود'. "\r\n";
			}



		}


	}
	public function edit_mo(){
		if($_POST){
			$id=$_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$this->base_model->update('model',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function delete_model()
	{
		if ($_POST) {
			$id = $_POST['id'];

			if($this->base_model->is_input_available($id, 'id', 'model')){

				$this->base_model->delete_rows_by_ids($id,'model');

			}
			if($this->base_model->is_input_available($id, 'model', 'product_attributes')){

				$data['model'] = 0;
				$this->base_model->update('product_attributes',array('model'=>$id) ,$data);
				echo 2;
			}


			echo 1;
		}
	}
	public function model_copy(){
		$id=$_POST['id'];
		date_default_timezone_set("Asia/Tehran");
		$name=$this->base_model->get_data('model','name',array('id'=>$id));
		if(isset($name[0])){
			echo $name1=$name[0]->name;
		}

		for ($j = 1; $j < 1000; $j++) {
			$result=$name1.'-copy('.$j.')';
			if($this->base_model->is_input_unique($result,'name','model')){
				$data['name'] = $result;
				$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');

				$IsActive=$this->base_model->get_data('model','IsActive',array('id'=>$id));
				if(isset($IsActive[0])){
					echo $data['IsActive']=$IsActive[0]->IsActive;
				}


				$this->base_model->insert('model', $data);
				break;
			}
			else{
				echo 'error';
			}
		}

	}
//model - end


//jens
	public function jens(){
		$data['title']='جدول جنس';
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/jens');
	}
	function jens_list(){
		$fetch_data = $this->base_model->jens_make_datatables();
		$data = array();

		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = "";
			$sub_array[] = '
			<a href="'. base_url('admin/info_jens/').$row->id.'">
			<span id="name_' . $row->id . '">'.$row->name.'</span>
			</a>';
			if($row->IsActive==0) {$sub_array[] = '<span style="color: red;font-jens: 12px;">غیرفعال</span>';}
			else if($row->IsActive==1){$sub_array[] = '<span style="color: green;font-jens: 12px;">فعال</span>';}
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->IsActive==0) {$sub_array[] = '<button type="button" id="active" id_jens="'.$row->id.'" class="btn btn-primary btn-xs">فعال</button>';}
			else if($row->IsActive==1){$sub_array[] = '<button type="button" id="deactive" id_jens="'.$row->id.'" class="btn btn-primary btn-xs">غیرفعال</button>';}
//			$sub_array[] = '
//			<button type="button" id="copy" id_jens="'.$row->id.'" class="btn btn-info btn-xs">کپی</button>
//			';
			$sub_array[] = '
			<button type="button" id="edit" id_jens="'.$row->id.'" class="btn btn-warning btn-xs">ویرایش</button>
			';
			$sub_array[] = '<button type="button" id="delete" id_jens="'.$row->id.'" class="btn btn-danger btn-xs">حذف</button>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->jens_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->jens_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}
	public function info_jens($id){
		$data['title']='محصولات مرتبط';

		$this->session->set_flashdata('id_jens', $id);

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/info_jens');
	}
	public function info_jens_list(){
		$fetch_data = $this->base_model->jens_attr_make_datatables();
		$data = array();
		$id = $this->session->flashdata('id_jens');
		foreach($fetch_data as $row)
		{if ($row->jens==$id){

			$sub_array = array();
			$sub_array[] = '';
			$sub_array[] = '<a href="'. base_url('admin/edit_product/').$row->code.'">
			<span>'.$row->name.'</span>
			</a>';
			$sub_array[] = $row->supply;
			$sub_array[] = $row->price;
			$sub_array[] = $row->off_percent;
			$sub_array[] = '';


			$data[] = $sub_array;
		}}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->jens_attr_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->jens_attr_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);

	}
	public function insert_jens()
	{
		if ($_POST) {
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$data['IsActive'] = 1;
			$this->base_model->insert('jens', $data);
			echo 1;
		}

	}
	public function get_jens($id)
	{
		$jens=$this->base_model->get_data('jens','name',array('id'=>$id));
		if (isset($jens[0])){
			echo $jens[0]->name;
		}


	}
	function search_jens()
	{
		$output = '';
		$query = '';
		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}

		$data = $this->base_model->search_jens($query);


		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
			{
				$output .= ' <a
				class="tag" id="tag_'.$row->id.'" id_c="'.$row->id.'" style="display: block;">'.$row->name.'</a> ';

			}
		} else {
			$output = 0;
		}
		echo $output;
	}
	function jens_validation(){
		echo $this->base_model->is_input_available('$_POST["name"]','name','jens');
		$data['name'] = $_POST['name'];
		$this->form_validation->set_rules('name', 'jens', 'required|trim|is_unique[jens.name]');

		if($this->form_validation->run())
		{
			echo 1;
		}else{
			if($this->base_model->is_input_available($_POST["name"],'name','jens') || $_POST["name"]==''){
				echo 2;
			}

		}
	}
	function check_jens()
	{

		if($this->base_model->is_input_available($_POST["name"],'name','jens'))
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}
		else
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}


	}
	function check_jens_in_tables()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$product_attr=$this->base_model->get_data('product_attributes','id',array('jens'=>$id));

			$i2=0;
			foreach ($product_attr as $p) {
				if ($this->base_model->is_input_available($id, 'jens', 'product_attributes')) {
					$i2++;
				}
			}
			if($i2>0){
				echo ' تعداد ' .$i2. ' محصول با این سایز ویرایش می شود'. "\r\n";
			}


		}


	}
	public function edit_je(){
		if($_POST){
			$id=$_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$this->base_model->update('jens',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function delete_jens()
	{
		if ($_POST) {
			$id = $_POST['id'];
			if($this->base_model->is_input_available($id, 'id', 'jens')){
				$this->base_model->delete_rows_by_ids($id,'jens');
			}
			if($this->base_model->is_input_available($id, 'jens', 'product_attributes')){
				$data['jens'] = 0;
				$this->base_model->update('product_attributes',array('jens'=>$id) ,$data);
				echo 2;
			}


			echo 1;
		}
	}
	public function jens_copy(){
		$id=$_POST['id'];
		date_default_timezone_set("Asia/Tehran");
		$name=$this->base_model->get_data('jens','name',array('id'=>$id));
		if(isset($name[0])){
			echo $name1=$name[0]->name;
		}

		for ($j = 1; $j < 1000; $j++) {
			$result=$name1.'-copy('.$j.')';
			if($this->base_model->is_input_unique($result,'name','jens')){
				$data['name'] = $result;
				$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');

				$IsActive=$this->base_model->get_data('jens','IsActive',array('id'=>$id));
				if(isset($IsActive[0])){
					echo $data['IsActive']=$IsActive[0]->IsActive;
				}

				$this->base_model->insert('jens', $data);
				break;
			}
			else{
				echo 'error';
			}
		}

	}
//jens - end


//brand
	public function brand(){
		$data['title']='جدول برند';
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/brand');
	}
	function brand_list(){
		$fetch_data = $this->base_model->brand_make_datatables();
		$data = array();

		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = "";
			$sub_array[] = '
			<a href="'. base_url('admin/info_brand/').$row->id.'">
			<span id="name_' . $row->id . '">'.$row->name.'</span>
			</a>';
			if($row->IsActive==0) {$sub_array[] = '<span style="color: red;font-brand: 12px;">غیرفعال</span>';}
			else if($row->IsActive==1){$sub_array[] = '<span style="color: green;font-brand: 12px;">فعال</span>';}
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->IsActive==0) {$sub_array[] = '<button type="button" id="active" id_brand="'.$row->id.'" class="btn btn-primary btn-xs">فعال</button>';}
			else if($row->IsActive==1){$sub_array[] = '<button type="button" id="deactive" id_brand="'.$row->id.'" class="btn btn-primary btn-xs">غیرفعال</button>';}
//			$sub_array[] = '
//			<button type="button" id="copy" id_brand="'.$row->id.'" class="btn btn-info btn-xs">کپی</button>
//			';
			$sub_array[] = '
			<button type="button" id="edit" id_brand="'.$row->id.'" class="btn btn-warning btn-xs">ویرایش</button>
			';
			$sub_array[] = '<button type="button" id="delete" id_brand="'.$row->id.'" class="btn btn-danger btn-xs">حذف</button>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->brand_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->brand_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}
	public function info_brand($id){
		$data['title']='محصولات مرتبط';

		$this->session->set_flashdata('id_brand', $id);

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/info_brand');
	}
	public function info_brand_list(){
		$fetch_data = $this->base_model->brand_attr_make_datatables();
		$data = array();
		$id = $this->session->flashdata('id_brand');
		foreach($fetch_data as $row)
		{if ($row->brand==$id){

			$sub_array = array();
			$sub_array[] = '';
			$sub_array[] = '<a href="'. base_url('admin/edit_product/').$row->code.'">
			<span>'.$row->name.'</span>
			</a>';
			$sub_array[] = $row->supply;
			$sub_array[] = $row->price;
			$sub_array[] = $row->off_percent;
			$sub_array[] = '';


			$data[] = $sub_array;
		}}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->brand_attr_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->brand_attr_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);

	}
	public function insert_brand()
	{
		if ($_POST) {
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$data['IsActive'] = 1;
			$this->base_model->insert('brand', $data);
			echo 1;
		}

	}
	public function get_brand($id)
	{
		$brand=$this->base_model->get_data('brand','name',array('id'=>$id));
		if (isset($brand[0])){
			echo $brand[0]->name;
		}


	}
	function search_brand()
	{
		$output = '';
		$query = '';
		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}

		$data = $this->base_model->search_brand($query);


		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
			{
				$output .= ' <a
				class="tag" id="tag_'.$row->id.'" id_c="'.$row->id.'" style="display: block;">'.$row->name.'</a> ';

			}
		} else {
			$output = 0;
		}
		echo $output;
	}
	function brand_validation(){
		echo $this->base_model->is_input_available('$_POST["name"]','name','brand');
		$data['name'] = $_POST['name'];
		$this->form_validation->set_rules('name', 'brand', 'required|trim|is_unique[brand.name]');

		if($this->form_validation->run())
		{
			echo 1;
		}else{
			if($this->base_model->is_input_available($_POST["name"],'name','brand') || $_POST["name"]==''){
				echo 2;
			}

		}
	}
	function check_brand()
	{

		if($this->base_model->is_input_available($_POST["name"],'name','brand'))
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}
		else
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}


	}
	function check_brand_in_tables()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$product_attr=$this->base_model->get_data('product_attributes','id',array('brand'=>$id));

			$i2=0;
			foreach ($product_attr as $p) {
				if ($this->base_model->is_input_available($id, 'brand', 'product_attributes')) {
					$i2++;
				}
			}
			if($i2>0){
				echo ' تعداد ' .$i2. ' محصول با این سایز ویرایش می شود'. "\r\n";
			}


		}


	}
	public function edit_br(){
		if($_POST){
			$id=$_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$this->base_model->update('brand',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function delete_brand()
	{
		if ($_POST) {
			$id = $_POST['id'];
			if($this->base_model->is_input_available($id, 'id', 'brand')){
				$this->base_model->delete_rows_by_ids($id,'brand');
			}
			if($this->base_model->is_input_available($id, 'brand', 'product_attributes')){
				$data['brand'] = 0;
				$this->base_model->update('product_attributes',array('brand'=>$id) ,$data);
				echo 2;
			}

			echo 1;
		}
	}
	public function brand_copy(){
		$id=$_POST['id'];
		date_default_timezone_set("Asia/Tehran");
		$name=$this->base_model->get_data('brand','name',array('id'=>$id));
		if(isset($name[0])){
			echo $name1=$name[0]->name;
		}


		for ($j = 1; $j < 1000; $j++) {
			$result=$name1.'-copy('.$j.')';
			if($this->base_model->is_input_unique($result,'name','brand')){
				$data['name'] = $result;
				$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');

				$IsActive=$this->base_model->get_data('brand','IsActive',array('id'=>$id));
				if(isset($IsActive[0])){
					echo $data['IsActive']=$IsActive[0]->IsActive;
				}


				$this->base_model->insert('brand', $data);
				break;
			}
			else{
				echo 'error';
			}
		}

	}
//brand - end


//color
	public function color(){
		$data['title']='جدول رنگ';
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/color');
	}
	function color_list(){
		$fetch_data = $this->base_model->color_make_datatables();
		$data = array();

		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = "";
			$sub_array[] = '<span style="height: 16px;width: 16px;border:1px solid black;border-radius: 50%;display: inline-block;background:'.$row->color_code.' "></span>
			<span id="name_' . $row->id . '">'.$row->name.'</span>';
			$sub_array[] = '
			<a href="'. base_url('admin/info_color/').$row->id.'">
			<span id="code_' . $row->id . '">'.$row->color_code.'</span>
			</a>';
			if($row->IsActive==0) {$sub_array[] = '<span style="color: red;font-color: 12px;">غیرفعال</span>';}
			else if($row->IsActive==1){$sub_array[] = '<span style="color: green;font-color: 12px;">فعال</span>';}
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->IsActive==0) {$sub_array[] = '<button type="button" id="active" id_color="'.$row->id.'" class="btn btn-primary btn-xs">فعال</button>';}
			else if($row->IsActive==1){$sub_array[] = '<button type="button" id="deactive" id_color="'.$row->id.'" class="btn btn-primary btn-xs">غیرفعال</button>';}
//			$sub_array[] = '
//			<button type="button" id="copy" id_color="'.$row->id.'" class="btn btn-info btn-xs">کپی</button>
//			';
			$sub_array[] = '
			<button type="button" id="edit" id_color="'.$row->id.'" class="btn btn-warning btn-xs">ویرایش</button>
			';
			$sub_array[] = '<button type="button" id="delete" id_color="'.$row->id.'" class="btn btn-danger btn-xs">حذف</button>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->color_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->color_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}
	public function info_color($id){
		$data['title']='محصولات مرتبط';

		$this->session->set_flashdata('id_color', $id);

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/info_color');
	}
	public function info_color_list(){
		$fetch_data = $this->base_model->color_attr_make_datatables();
		$data = array();
		$id = $this->session->flashdata('id_color');
		foreach($fetch_data as $row)
		{if ($row->color==$id){

			$sub_array = array();
			$sub_array[] = '';
			$sub_array[] = '<a href="'. base_url('admin/edit_product/').$row->code.'">
			<span>'.$row->name.'</span>
			</a>';
			$sub_array[] = $row->supply;
			$sub_array[] = $row->price;
			$sub_array[] = $row->off_percent;
			$sub_array[] = '';


			$data[] = $sub_array;
		}}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->color_attr_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->color_attr_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);

	}
	public function insert_color()
	{
		if ($_POST) {
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$data['color_code'] = $_POST['code'];
			$data['IsActive'] = 1;
			$this->base_model->insert('color', $data);
			echo 1;
		}

	}
	public function get_color($id)
	{
		$color=$this->base_model->get_data('color','color_code',array('id'=>$id));
		if (isset($color[0])){
			echo $color[0]->color_code;
		}


	}

	function color_validation(){
		echo $this->base_model->is_input_available('$_POST["code"]','color_code','color');
		$data['color_code'] = $_POST['code'];
		$this->form_validation->set_rules('code', 'color', 'required|trim|is_unique[color.color_code]');

		if($this->form_validation->run())
		{
			echo 1;
		}else{
			if($this->base_model->is_input_available($_POST["code"],'color_code','color') || $_POST["code"]==''){
				echo 2;
			}

		}
	}
	function check_color()
	{

		if($this->base_model->is_input_available($_POST["code"],'color_code','color'))
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}
		else
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}


	}

	function color_validation2()
	{
		$id_color = $_POST['id'];
		$this->form_validation->set_message('_edit_unique_color', 'در سیستم موجود است');

		$this->form_validation->set_rules('code', 'color', 'trim|required|callback__edit_unique_color[' . $id_color . ']');

		if ($this->form_validation->run()) {
			echo 1;
		} else {
			if (!$this->form_validation->run() || $_POST['code']='') {
				echo 2;
			}
		}
	}
	function check_color2()
	{
		$x = $_POST['code'];
		$y = $_POST['id'];

		if($this->base_model->check_value('color',array('color_code'=>$x,'id!='=>$y)))
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}
		else
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}


	}
	function _edit_unique_color($x,$y)
	{
		$return_value = $this->base_model->check_value('color',array('color_code'=>$x,'id!='=>$y));
		if ($return_value)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function check_color_in_tables()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$product_attr=$this->base_model->get_data('product_attributes','id',array('color'=>$id));

			$i2=0;
			foreach ($product_attr as $p) {
				if ($this->base_model->is_input_available($id, 'color', 'product_attributes')) {
					$i2++;
				}
			}
			if($i2>0){
				echo ' تعداد ' .$i2. ' محصول با این رنگ ویرایش می شود'. "\r\n";
			}


		}


	}
	public function edit_co(){
		if($_POST){
			$id=$_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$data['color_code'] = $_POST['code'];
			$this->base_model->update('color',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function delete_color()
	{
		if ($_POST) {
			$id = $_POST['id'];
			if($this->base_model->is_input_available($id, 'id', 'color')){
				$this->base_model->delete_rows_by_ids($id,'color');
			}
			if($this->base_model->is_input_available($id, 'color', 'product_attributes')){
				$data['color'] = 0;
				$this->base_model->update('product_attributes',array('color'=>$id) ,$data);
				echo 2;
			}

			echo 1;
		}
	}

//color - end



	public function edit_product($code)
	{
		$data['title']='ویرایش محصول';
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['images']=$this->base_model->get_data('images','*');

		$data['products'] = $this->base_model->get_data('products', '*',array('code'=>$code));
		$data['x'] = $this->base_model->get_join_left('*', 'products', 'product_attributes', 'products.code=product_attributes.code_p', array('code'=>$code));
		$data['y'] = $this->base_model->get_join_left('*', 'product_attributes', 'products', 'products.code=product_attributes.code_p', array('code'=>$code));

		$data['model']=$this->base_model->get_data('model','*');
		$data['jens']=$this->base_model->get_data('jens','*');
		$data['brand']=$this->base_model->get_data('brand','*');
		$data['color']=$this->base_model->get_data('color','*');
		$data['size']=$this->base_model->get_data('size','*');

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/edit-product');

	}
	public function edit_productttt($id)
	{
		$data['title']='ویرایش محصول';
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['images']=$this->base_model->get_data('images','*');
		$data['products'] = $this->base_model->get_data('products', '*',array('id'=>$id));
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/edit-product');
	}
	public function edit_prd(){
		$data2=[];
    	if ($_POST) {
			$id = $_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['code'] = $_POST['code'];
			$data['id_cat1'] = $_POST['id_cat1'];
			$data['id_cat2'] = $_POST['id_cat2'];
			$data['name'] = $_POST['name'];
			$data['text'] = $_POST['text'];

			$data2['name'] = $_POST['name'];
			if($this->base_model->is_input_available($id, 'id_p', 'shopping_cart')){
				$this->base_model->update('shopping_cart', array('id_p' => $id), $data2);
				echo 2;
			}else{
				echo 3;
			}

			$this->base_model->update('products', array('id' => $id), $data);

			echo 1;
		}
	}
	public function edit_prd2(){
		$data2=[];
		if ($_POST) {
			$id = $_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['code'] = $_POST['code'];
			$data['id_cat1'] = $_POST['id_cat1'];
			$data['id_cat2'] = $_POST['id_cat2'];
			$data['name'] = $_POST['name'];
			$data['model'] = $_POST['model'];
			$data['jens'] = $_POST['jens'];
			$data['size'] = $_POST['size'];
			$data['brand'] = $_POST['brand'];
			$data['price'] = $_POST['price'];
			$data['off_percent'] = $_POST['off_percent'];
			$data['text'] = $_POST['text'];

			$data3=$this->base_model->get_data('shopping_cart','*');

			$p=$_POST['price'];
			$op=$_POST['off_percent'];
			$name=$_POST['name'];
			if($_POST['off_percent']==null){
				$pri=$_POST['price'];
			}else{
				$pri=$p-($op/100)*$p;
			}
			foreach ($data3 as $row){
				if($row->id_p==$id){
					$qty=$row->qty;
					$data2['name'] = $name;
					$data2['price'] = $p;
					$data2['off_percent'] = $op;
					$data2['off_price'] = ($p-$pri)*$qty;
					$data2['total_price1'] = $qty*$p;
					$data2['total_price'] = $qty*$pri;
					$data2['final_price'] = $qty*$pri;
				}
			}
			$this->base_model->update('products', array('id' => $id), $data);
			if($this->base_model->is_input_available($id, 'id_p', 'shopping_cart')){
				$this->base_model->update('shopping_cart', array('id_p' => $id), $data2);
				echo 2;
			}else{
				echo 3;
			}


			echo 1;
		}
	}
	public function delete_prd()
	{
		if ($_POST) {
			$id_p = $_POST['id_p'];
			$code_p = $_POST['code_p'];
			$this->base_model->delete_row('products', 'id', $id_p);
			$this->base_model->delete_row('shopping_cart', 'id_p', $id_p);
			$this->base_model->delete_row('product_attributes', 'code_p',$code_p);
			$this->base_model->delete_row('comments', 'code',$code_p);
			echo 1;
		}
	}
	public function delete_products() {
		if (isset($_POST['ids'])) {
			$ids = explode(',', $_POST['ids']);
			$codes = explode(',', $_POST['codes']);
			$id_p = $ids;
			$results = $this->base_model->delete_rows_by_ids($ids,'products');
			$results2 = $this->base_model->delete_rows_by_ids($id_p,'shopping_cart');
			$results3 = $this->base_model->delete_rows_by_col('code_p',$codes,'product_attributes');

			if ($results === TRUE && $results2 === TRUE && $results3 === TRUE) {
				echo '<span style="color:green;">Product(s) successfully deleted</span>';
			} else {
				echo '<span style="color:red;">Something went wrong during product deletion</span>';
			}
		} else {
			echo '<span style="color:red;">You must select at least one product for deletion</span>';
		}
	}
	public function products()
	{
		$data['title']='محصولات';
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/products');
	}
	function products_list()
	{

		$fetch_data = $this->base_model->products_make_datatables();
		$data = array();
		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" value="'.$row->id.'" code_p="'.$row->code.'"></input> ';
			$sub_array[] = '<span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
			title="'.$row->name.'">'.$row->name.'</span>';
			$sub_array[] = '<span>'.number_format($row->price).'</span>';
			$sub_array[] = '<span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;"
			title="'.$row->name_cat1.'">'.$row->name_cat1.'</span>';
			$sub_array[] = '<span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;"
			title="'.$row->name_cat2.'">'.$row->name_cat2.'</span>';
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->isActive==0) {$sub_array[] = '<button type="button" id="active" id_p="'.$row->id.'" class="btn btn-primary btn-xs">فعال سازی</button>';}
			else if($row->isActive==1){$sub_array[] = '<button type="button" id="deactive" id_p="'.$row->id.'" class="btn btn-secondary btn-xs">غیرفعال سازی</button>';}
			$sub_array[] = '
			<button type="button" id="copy" id_p="'.$row->id.'" code_p="'.$row->code.'" class="btn btn-info">
			<i class="fa fa-copy"></i>
			</button>
			';
			$sub_array[] = '<a href="'. base_url('admin/edit_product/').$row->code.'">
			<button type="button" id="edit" code_p="'.$row->code.'" class="btn btn-warning">
			<i class="fa fa-edit" ></i>
			</button>
			</a>';
			$sub_array[] = '<button type="button" id="delete" id_p="'.$row->id.'" code_p="'.$row->code.'" class="btn btn-danger">
			<i class="fa fa-trash"></i>
			</button>';
			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->products_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->products_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}
	public function attributes()
	{
		$data['title']='انبارداری';
		$data['product_attributes']=$this->base_model->get_data('product_attributes','*');
		$data['products']=$this->base_model->get_data('products','*');

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/attributes');
	}
	public function attrs_list(){
		$fetch_data = $this->base_model->attrs_make_datatables();
		$data = array();
		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" value="'.$row->id.'" code_p="'.$row->code.'"></input> ';
			$sub_array[] = '<span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
			title="'.$row->code_p.'">
			'.$row->code_p.'
			</span>';
			$sub_array[] = '<span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
			title="'.$row->name.'">'.$row->name.'</span>';

			$sub_array[] = '<span>'.number_format($row->buy_price).'</span>';
			$sub_array[] = '<span>'.number_format($row->price).'</span>';
			$sub_array[] = '<span>'.number_format($row->supply).'</span>';
			$sub_array[] = '<span>'.$row->unit.'</span>';
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->isActive==0) {$sub_array[] = '<button type="button" id="active" id_attr="'.$row->id.'" class="btn btn-primary btn-xs">فعال سازی</button>';}
			else if($row->isActive==1){$sub_array[] = '<button type="button" id="deactive" id_attr="'.$row->id.'" class="btn btn-secondary btn-xs">غیرفعال سازی</button>';}
			$sub_array[] = '<a href="'. base_url('admin/inventory_attrs/').$row->id.'">
			<button type="button" id="report" id_attr="'.$row->id.'" class="btn btn-info">
			<i class="fa fa-bars"></i>
			</button>
			</a>';
			$sub_array[] = '<a href="'. base_url('admin/edit_product/').$row->code.'">
			<button type="button" id="edit" code_p="'.$row->code.'" class="btn btn-warning">
			<i class="fa fa-edit" ></i>
			</button>
			</a>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->attrs_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->attrs_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}


	public function inventory(){
		$data['title']='لیست انبار';
		$data['product_attributes']=$this->base_model->get_data('product_attributes','*');
		$data['products']=$this->base_model->get_data('products','*');
		$data['inventory_attrs']=$this->base_model->get_data('inventory_attrs','*');
		$data['inventory']=$this->base_model->get_data('inventory','*');

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/inventory');
	}
	public function inventory_list(){
		$fetch_data = $this->base_model->inventory_make_datatables();
		$data = array();
		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" value="'.$row->id_inv.'" id_inv="'.$row->id_inv.'" ></input> ';
			$sub_array[] = '<span id="name_'.$row->id_inv.'" style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
			title="'.$row->name.'">
			'.$row->name.'
			</span>';
			$sub_array[] = '<span id="details_'.$row->id_inv.'" style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
			title="'.$row->details.'">'.$row->details.'</span>';

			$sub_array[] = '<span>'.number_format($row->qty).'</span>';
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->isActive==0) {$sub_array[] = '<button type="button" id="active" id_inv="'.$row->id_inv.'" class="btn btn-primary btn-xs">فعال سازی</button>';}
			else if($row->isActive==1){$sub_array[] = '<button type="button" id="deactive" id_inv="'.$row->id_inv.'" class="btn btn-secondary btn-xs">غیرفعال سازی</button>';}
			$sub_array[] = '<a href="'. base_url('admin/inventory_attrs/').$row->id_inv.'">
			<button type="button" id="report" id_inv="'.$row->id_inv.'" class="btn btn-info ">
			<i class="fa fa-list" ></i>
			</button>
			</a>';
			$sub_array[] = '<a href="'. base_url('admin/inventory_supply/').$row->id_inv.'">
			<button type="button" id="supply" id_inv="'.$row->id_inv.'" class="btn btn-primary ">
			<i class="fa fa-cube" ></i>
			</button>
			</a>';
			$sub_array[] = '
			<button type="button" id="edit" id_inv="'.$row->id_inv.'" class="btn btn-warning ">
			<i class="fa fa-edit" ></i>
			</button>
			';
			$sub_array[] = '<button type="button" id="delete" id_inv="'.$row->id_inv.'" class="btn btn-danger">
			<i class="fa fa-trash" ></i>
			</button>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->inventory_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->inventory_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}


	public function insert_inv(){

		if ($_POST) {
			$this->load->library('form_validation');
			$this->load->helper('form');
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$data['details'] = $_POST['details'];
			$data['isActive'] = '1';

			$this->form_validation->set_message('required', 'فیلد الزامی');
			$this->form_validation->set_message('is_unique[inventory.name]', 'این مقدار تکراری است');

			$this->form_validation->set_rules('name', 'کد تخفیف', 'trim|required|is_unique[inventory.name]');

			if ($this->form_validation->run()) {
				if ($_POST) {
					date_default_timezone_set("Asia/Tehran");
					$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
					$data['name'] = $_POST['name'];
					$data['details'] = $_POST['details'];
					$data['isActive'] = '1';

					$this->base_model->insert('inventory',$data);

					redirect('admin/inventory');

				}

			} else {
				$this->inventory();
			}
		}

	}

	function search_name()
	{
		$output = '';
		$query = '';
		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}

		$data = $this->base_model->search_name($query);


		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
			{
				$output .= ' <a
				class="tag" id="tag_'.$row->id.'" id_c="'.$row->id.'" style="display: block;">'.$row->name.'</a> ';

			}
		} else {
			$output = 0;
		}
		echo $output;
	}
	function check_name()
	{

		if($this->base_model->is_input_available($_POST["name"],'name','inventory'))
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}
		else
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}

	}


	public function edit_inv(){

		if ($_POST) {
			$this->load->library('form_validation');
			$this->load->helper('form');
			$id = $_POST['id_modal'];
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name_modal'];
			$data['details'] = $_POST['details_modal'];
			$data['isActive'] = '1';

			$this->form_validation->set_message('required', 'فیلد الزامی');
			$this->form_validation->set_message('_edit_unique_name', 'در سیستم موجود است');

			$this->form_validation->set_rules('name_modal', 'کد تخفیف', 'trim|required|callback__edit_unique_name['.$id.']');

			if ($this->form_validation->run()) {
				if ($_POST) {
					$id = $_POST['id_modal'];
					date_default_timezone_set("Asia/Tehran");
					$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
					$data['name'] = $_POST['name_modal'];
					$data['details'] = $_POST['details_modal'];
					$data['isActive'] = '1';

					$this->base_model->update('inventory', array('id'=>$id), $data);

					redirect('admin/inventory');

				}

			} else {
				$this->inventory();
			}
		}

	}

	function _edit_unique_name($x,$y)
	{
		$return_value = $this->base_model->check_value('inventory',array('name'=>$x,'id!='=>$y));
		if ($return_value)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function search_name2()
	{
		$output = '';
		$query = '';
		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}

		$data = $this->base_model->search_name($query);


		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
			{
				$output .= ' <a
				class="tag2" id="tag2_'.$row->id.'" id_c2="'.$row->id.'" style="display: block;">'.$row->name.'</a> ';

			}
		} else {
			$output = 0;
		}
		echo $output;
	}
	function check_name2()
	{

		if($this->base_model->check_value('inventory',array('name'=>$_POST["name"],'id!='=>$_POST["id"])) )
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}
		else
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}


	}

	public function inventory_supply($id){
		$data['title']='موجودی انبار';
		$data['id']=$id;
		$data['product_attributes']=$this->base_model->get_data('product_attributes','*');
		$data['products']=$this->base_model->get_data('products','*');
		$data['inventory_attrs']=$this->base_model->get_data('inventory_attrs','*');
		$data['inventory_supply']=$this->base_model->get_data('inventory_supply','*',array('id_inv'=>$id));
		$data['inventory']=$this->base_model->get_data('inventory','*',array('id'=>$id));

		$data['size']=$this->base_model->get_data('size','*');
		$data['model']=$this->base_model->get_data('model','*');
		$data['color']=$this->base_model->get_data('color','*');
		$data['brand']=$this->base_model->get_data('brand','*');
		$data['jens']=$this->base_model->get_data('jens','*');

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/inventory_supply');
	}

	public function insert_inv_sup(){
		if ($_POST) {
			$this->load->library('form_validation');
			$this->load->helper('form');
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['id_attr'] = $_POST['aaa'];
			$id_attr = $_POST['aaa'];
			$data['supply'] = $_POST['supply'];
			$data['details'] = $_POST['details'];
			$data['isActive'] = '1';
			$id_inv = $_POST['id_inv'];

			$this->form_validation->set_message('required', 'فیلد الزامی');
			$this->form_validation->set_rules('aaa', 'نام محصول', 'trim|required');

			if ($this->form_validation->run()) {
				if ($_POST) {
					$id_inv = $_POST['id_inv'];
					$id_attr = $_POST['aaa'];
					$inventory_supply=$this->base_model->get_data('inventory_supply','*',array('id_inv'=>$id_inv,'id_attr'=>$id_attr));
					if(isset($inventory_supply[0])){
						$supply = $inventory_supply[0]->supply;
					}
					$product_attributes=$this->base_model->get_data('product_attributes','*',array('id'=>$id_attr));
					if(isset($product_attributes[0])){
						$code_p = $product_attributes[0]->code_p;
						$unit = $product_attributes[0]->unit;
						$price = $product_attributes[0]->price;
						$buy_price = $product_attributes[0]->buy_price;
					}

					date_default_timezone_set("Asia/Tehran");
					$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
					$data['id_attr'] = $_POST['aaa'];
					$data['supply'] = $_POST['supply'];
					$data['details'] = $_POST['details'];
					$data['isActive'] = '1';
					$data['code_p'] = $code_p;
					$data['id_inv'] = $id_inv;

					date_default_timezone_set("Asia/Tehran");
					$data2['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
					$s = $_POST['supply'];
					$data2['supply'] = $s+$supply;
					$data2['details'] = $_POST['details'];

					date_default_timezone_set("Asia/Tehran");
					$data3['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
					$data3['id_inv_from'] = '0';
					$data3['id_inv_to'] = $_POST['id_inv'];
					$data3['date'] = $this->date_j(date('Y-m-d'));
					$data3['id_attr'] = $_POST['aaa'];
					$data3['code_p'] = $code_p;
					$data3['unit'] = $unit;
					$data3['qty'] = $_POST['supply'];
					$data3['buy_price'] = $buy_price;
					$data3['price'] = $price;
					$data3['details'] = 'خرید';
					$data3['isActive'] = '1';


					if(isset($inventory_supply[0])){
						$this->base_model->update('inventory_supply',array('id_inv'=>$id_inv,'id_attr'=>$id_attr),$data2);
						$this->base_model->insert('inventory_attrs',$data3);
					}else{
						$this->base_model->insert('inventory_supply',$data);
						$this->base_model->insert('inventory_attrs',$data3);
					}


					redirect('admin/inventory_supply/'.$id_inv);

				}

			} else {
				$this->inventory();
			}
		}

	}

	public function inventory_supply_list($id){

		$id_inv = $id;
		$fetch_data = $this->base_model->inv_sup_make_datatables(array('inventory_supply.id_inv'=>$id_inv));
		$data = array();
		foreach($fetch_data as $row)
		{
			if ($row->id_inv==$id_inv ) {
				$sub_array = array();
				$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" value="' . $row->id_attr . '" id_attr="' . $row->id_attr . '" ></input> ';
				$sub_array[] = '<span id="code_' . $row->id_attr . '" style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
			title="' . $row->code . '">
			' . $row->code . '
			</span>';
				$sub_array[] = '<span id="name_' . $row->id_attr . '" style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
			title="' . $row->name . ' - ' . $row->price . ''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':'(').''.($row->moId==$row->model?"مدل: $row->moName ":'').''.($row->siId==$row->size?"سایز: $row->size ":'').''.($row->jeId==$row->jens?"جنس: $row->jens ":'').''.($row->brId==$row->brand?"برند: $row->brand ":'').''.($row->coId==$row->color?"رنگ: $row->color ":'').''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':')').'">
			' . $row->name . ' - ' . $row->price . ''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':'(').''.($row->moId==$row->model?"مدل: $row->moName ":'').''.($row->siId==$row->size?"سایز: $row->size ":'').''.($row->jeId==$row->jens?"جنس: $row->jens ":'').''.($row->brId==$row->brand?"برند: $row->brand ":'').''.($row->coId==$row->color?"رنگ: $row->color ":'').''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':')').'
			</span>';
				$sub_array[] = '<span>' . number_format($row->supply) . '</span>';
				$sub_array[] = $row->created;
				$sub_array[] = $row->modified;
				if ($row->isActive == 0) {
					$sub_array[] = '<button type="button" id="active" id_attr="' . $row->id_attr . '" class="btn btn-primary btn-xs">فعال سازی</button>';
				} else if ($row->isActive == 1) {
					$sub_array[] = '<button type="button" id="deactive" id_attr="' . $row->id_attr . '" class="btn btn-secondary btn-xs">غیرفعال سازی</button>';
				}
				$sub_array[] = '
			<button type="button" id="edit" id_attr="' . $row->id_attr . '" class="btn btn-warning ">
			<i class="fa fa-edit" ></i>
			</button>
			';
				$sub_array[] = '<button type="button" id="delete" id_attr="' . $row->id_attr . '" class="btn btn-danger">
			<i class="fa fa-trash" ></i>
			</button>';

				$data[] = $sub_array;
			}
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->inv_sup_get_all_data(array('inventory_supply.id_inv'=>$id_inv)),
			"recordsFiltered"     =>     $this->base_model->inv_sup_get_filtered_data(array('inventory_supply.id_inv'=>$id_inv)),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}

	public function insert_inventory_attrs(){
		$data['title']='افزودن اطلاعات کالا';
		$data['register']=$this->base_model->get_data('register','*');
		$data['profile']=$this->base_model->get_data('profile','*');
		$data['inventory']=$this->base_model->get_data('inventory','*');
		$data['inventory_attrs']=$this->base_model->get_data('inventory_attrs','*');
		$data['products']=$this->base_model->get_data('products','*');
		$data['product_attributes']=$this->base_model->get_data('product_attributes','*');
		$data['size']=$this->base_model->get_data('size','*');
		$data['model']=$this->base_model->get_data('model','*');
		$data['color']=$this->base_model->get_data('color','*');
		$data['brand']=$this->base_model->get_data('brand','*');
		$data['jens']=$this->base_model->get_data('jens','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/insert_inventory_attrs');
	}

	public function add_inv_attr(){
		if ($_POST) {
			$this->load->library('form_validation');
			$this->load->helper('form');
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['id_inv_from'] = $_POST['inv_from'];
			$inv_from = $_POST['inv_from'];
			$data['id_inv_to'] = $_POST['inv_to'];
			$inv_to = $_POST['inv_to'];
			$data['date'] = $_POST['datetime2'];
			$data['id_attr'] = $_POST['aaa'];
			$id_attr = $_POST['aaa'];
			$data['unit'] = $_POST['unit'];
			$unit = $_POST['unit'];
			$data['qty'] = $_POST['qty'];
			$qty = $_POST['qty'];
			$max = $_POST['max']+1;
			$data['buy_price'] = $_POST['buy_price'];
			$data['price'] = $_POST['price'];
			$data['details'] = $_POST['details'];
			$data['isActive'] = '1';

			$product_attributes=$this->base_model->get_data('product_attributes','*',array('id'=>$id_attr));
			if (isset($product_attributes[0])){
				$code_p = $product_attributes[0]->code_p;
			}

			$x=$this->base_model->get_data('inventory_supply','*',array('id_inv'=>$inv_from, 'id_attr'=>$id_attr));
			if (isset($x[0])){
				$supply = $x[0]->supply;
			} else {
				$supply = 0;
			}
			$x1=$this->base_model->get_data('inventory_supply','*',array('id_inv'=>$inv_to, 'id_attr'=>$id_attr));
			if (isset($x1[0])){
				$id1 = $x1[0]->id;
				$supply1 = $x1[0]->supply;
			} else {
				$id1 = '';
				$supply1 = 0;
			}


			$this->form_validation->set_message('required', 'فیلد الزامی');
			$this->form_validation->set_message('less_than', '%s باید کمتر از %d باشد');
			$this->form_validation->set_message('select_required', 'فیلد الزامی');

			$this->form_validation->set_rules('inv_from', 'انبار مبدا', 'trim|required|callback_select_required['.$inv_from.']');
			$this->form_validation->set_rules('inv_to', 'انبار مقصد', 'trim|required|callback_select_required['.$inv_to.']');
			$this->form_validation->set_rules('date', 'تاریخ', 'trim|required');
			$this->form_validation->set_rules('aaa', 'محصول', 'trim|required|callback_select_required['.$id_attr.']');
			$this->form_validation->set_rules('unit', 'واحد', 'trim|required|callback_select_required['.$unit.']');
			$this->form_validation->set_rules('qty', 'تعداد', 'trim|required');
			$this->form_validation->set_rules('buy_price', 'قیمت خرید', 'trim|required');
			$this->form_validation->set_rules('price', 'قیمت فروش', 'trim|required');
			$this->form_validation->set_rules('price', 'قیمت فروش', 'trim|required|greater_than['.$_POST['buy_price'].']');
			$this->form_validation->set_rules('qty', 'تعداد', 'trim|required|less_than['.$max.']');

			if ($this->form_validation->run()) {
				date_default_timezone_set("Asia/Tehran");
				$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data['id_inv_from'] = $_POST['inv_from'];
				$inv_from = $_POST['inv_from'];
				$data['id_inv_to'] = $_POST['inv_to'];
				$inv_to = $_POST['inv_to'];
				$data['date'] = $_POST['datetime2'];
				$data['id_attr'] = $_POST['aaa'];
				$id_attr = $_POST['aaa'];
				$data['code_p'] = $code_p;
				$data['unit'] = $_POST['unit'];
				$data['qty'] = $_POST['qty'];
				$qty = $_POST['qty'];
				$data['buy_price'] = $_POST['buy_price'];
				$data['price'] = $_POST['price'];
				$data['details'] = $_POST['details'];
				$data['isActive'] = '1';

				date_default_timezone_set("Asia/Tehran");
				$data2['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data2['supply'] = $supply-$qty;

				date_default_timezone_set("Asia/Tehran");
				$data3['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data3['supply'] = $supply1+$qty;

				$data4['id_inv'] = $inv_to;
				$data4['id_attr'] = $id_attr;
				$data4['code_p'] = $code_p;
				$data4['supply'] = $qty;
				$data4['isActive'] = '1';
				date_default_timezone_set("Asia/Tehran");
				$data4['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');

				$this->base_model->insert('inventory_attrs', $data);

				$this->base_model->update('inventory_supply',array('id_inv'=>$inv_from, 'id_attr'=>$id_attr) ,$data2);

				if($id1==''){
					$this->base_model->insert('inventory_supply', $data4);

				}else {
					$this->base_model->update('inventory_supply',array('id_inv'=>$inv_to, 'id_attr'=>$id_attr) ,$data3);
				}

				redirect('admin/insert_inventory_attrs');


			} else {
				$this->insert_inventory_attrs();
			}
		}
	}
	function select_required($x)
	{
		if($x == '0')
		{
			return FALSE;
		}
		return TRUE;
	}
	public function inventory_attrs($id){
		$data['title']='ویرایش انبار';
		$data['id']=$id;
		$data['product_attributes']=$this->base_model->get_data('product_attributes','*');
		$data['products']=$this->base_model->get_data('products','*');
		//or
		$data['inventory_attrs']=$this->base_model->get_data('inventory_attrs','*',array('id_inv_from'=>$id),NULL, array('id_inv_to'=>$id));
		$data['inventory']=$this->base_model->get_data('inventory','*',array('id'=>$id));
		$data['register']=$this->base_model->get_data('register','*');
		$data['profile']=$this->base_model->get_data('profile','*');

		$data['size']=$this->base_model->get_data('size','*');
		$data['model']=$this->base_model->get_data('model','*');
		$data['color']=$this->base_model->get_data('color','*');
		$data['brand']=$this->base_model->get_data('brand','*');
		$data['jens']=$this->base_model->get_data('jens','*');

//		$this->session->set_flashdata('id_inv', $id);

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/inventory_attrs');
	}
	public function inventory_attrs_list($id){

//		$id = $this->session->flashdata('id_inv');

		$id_inv = $id;
		$fetch_data = $this->base_model->inventory_attrs_make_datatables(array('inventory_attrs.id_inv_from'=>$id_inv) || array('inventory_attrs.id_inv_to'=>$id_inv));
		$data = array();

		foreach($fetch_data as $row)
		{
			if ($row->id_inv_from==$id_inv || $row->id_inv_to==$id_inv){

				$sub_array = array();
				$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" value="'.$row->row_id.'" id_row="'.$row->row_id.'" ></input> ';
				$sub_array[] = '<span style="display: inline-block;width: 30px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
				title="'.$row->code_p.'">
				'.$row->code_p.'
				</span>';
				$sub_array[] = '<span id="name_' . $row->id_attr . '" style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
			title="' . $row->pname . ' - ' . $row->price . ''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':'(').''.($row->moId==$row->model?"مدل: $row->moName ":'').''.($row->siId==$row->size?"سایز: $row->size ":'').''.($row->jeId==$row->jens?"جنس: $row->jens ":'').''.($row->brId==$row->brand?"برند: $row->brand ":'').''.($row->coId==$row->color?"رنگ: $row->color ":'').''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':')').'">
			' . $row->pname . ' - ' . $row->price . ''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':'(').''.($row->moId==$row->model?"مدل: $row->moName ":'').''.($row->siId==$row->size?"سایز: $row->size ":'').''.($row->jeId==$row->jens?"جنس: $row->jens ":'').''.($row->brId==$row->brand?"برند: $row->brand ":'').''.($row->coId==$row->color?"رنگ: $row->color ":'').''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':')').'
			</span>';
	//			$sub_array[] = '<span>'.number_format($row->buy_price).'</span>';
	//			$sub_array[] = '<span>'.number_format($row->price).'</span>';

				$sub_array[] = '<span style="display: inline-block;width: 50%;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
				title="'.$row->details.'">'.$row->details.'</span>';
				$sub_array[] = '<span>'.number_format($row->qty).'</span>';
				$sub_array[] = $row->unit;
				if($row->id_inv_from==$id_inv){$sub_array[] = 'خروج';}
				else if($row->id_inv_to==$id_inv){$sub_array[] = 'ورود';}
				$sub_array[] = '<span>'.$row->inv1_name.'</span>';
				$sub_array[] = '<span>'.$row->inv2_name.'</span>';
				$sub_array[] = $row->date;
				$sub_array[] = $row->created;
				$sub_array[] = $row->modified;
				if($row->isActive==0) {$sub_array[] = '<button type="button" id="active" id_row="'.$row->row_id.'" class="btn btn-primary btn-xs">فعال سازی</button>';}
				else if($row->isActive==1){$sub_array[] = '<button type="button" id="deactive" id_row="'.$row->row_id.'" class="btn btn-secondary btn-xs">غیرفعال سازی</button>';}
				$sub_array[] = '<a href="'. base_url('admin/edit_inventory_attrs/').$row->row_id.'">
				<button type="button" id="edit" id_row="'.$row->row_id.'" class="btn btn-warning ">
				<i class="fa fa-edit" ></i>
				</button>
				</a>';
				$sub_array[] = '<button type="button" id="delete" id_row="'.$row->row_id.'" class="btn btn-danger">
	<i class="fa fa-trash" ></i>
	</button>';

				$data[] = $sub_array;
			}
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->inventory_attrs_get_all_data(array('inventory_attrs.id_inv_from'=>$id_inv) || array('inventory_attrs.id_inv_to'=>$id_inv)),
			"recordsFiltered"     =>     $this->base_model->inventory_attrs_get_filtered_data(array('inventory_attrs.id_inv_from'=>$id_inv) || array('inventory_attrs.id_inv_to'=>$id_inv)),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}


	public function edit_inventory_attrs($id){
		$data['title']='ویرایش انبار';
		$data['product_attributes']=$this->base_model->get_data('product_attributes','*');
		$data['products']=$this->base_model->get_data('products','*');
		$data['inventory_attrs']=$this->base_model->get_data('inventory_attrs','*',array('id'=>$id));
		$data['inventory']=$this->base_model->get_data('inventory','*');
		$data['register']=$this->base_model->get_data('register','*');
		$data['profile']=$this->base_model->get_data('profile','*');

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/edit_inventory_attrs');
	}


	public function update_products_checkall(){
		if ($_POST) {
			$id = explode(',', $_POST['id']);
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			if($_POST['id_cat1']!='' && $_POST['id_cat2']!=''){
			$data['id_cat1'] = $_POST['id_cat1'];}
			if($_POST['id_cat2']!=''){
			$data['id_cat2'] = $_POST['id_cat2'];}
			if($_POST['name']!=''){
			$data['name'] = $_POST['name'];}
			if($_POST['text']!=''){
			$data['text'] = $_POST['text'];}

//			if($_POST['name']!=''){
//				$data2['name'] = $_POST['name'];}

			$this->base_model->update_rows_by_col('id',$id,'products',$data);

//			if($this->base_model->is_input_available($id, 'id_p', 'shopping_cart')){
//				$this->base_model->update_rows_by_col('id_p',$id,'shopping_cart',$data2);
//				echo 2;
//			}else{
//				echo 3;
//			}


			echo 1;
		}
	}

	public function row_active(){
		if($_POST){
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$id=$_POST['id'];
			$table=$_POST['table'];
			$data['IsActive'] = 1;
			$this->base_model->update($table,array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function row_deactive(){
		if($_POST){
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$id=$_POST['id'];
			$table=$_POST['table'];
			$data['IsActive'] = 0;
			$this->base_model->update($table,array('id'=>$id) ,$data);
			echo 1;
		}
	}

	public function rows_active(){
		if($_POST){
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['IsActive'] = 1;
			$id = explode(',', $_POST['id']);
			$table=$_POST['table'];
			$this->base_model->update_rows_by_col('id',$id,$table,$data);
			echo 1;
		}
	}
	public function rows_deactive(){
		if($_POST){
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['IsActive'] = 0;
			$id = explode(',', $_POST['id']);
			$table=$_POST['table'];
			$this->base_model->update_rows_by_col('id',$id,$table,$data);
			echo 1;
		}
	}

	public function row_active_by_col(){
		if($_POST){
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$val=$_POST['val'];
			$table=$_POST['table'];
			$col=$_POST['col'];
			$data['IsActive'] = 1;
			$this->base_model->update($table,array($col=>$val) ,$data);
			echo 1;
		}
	}
	public function row_deactive_by_col(){
		if($_POST){
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$val=$_POST['val'];
			$table=$_POST['table'];
			$col=$_POST['col'];
			$data['IsActive'] = 0;
			$this->base_model->update($table,array($col=>$val) ,$data);
			echo 1;
		}
	}
	public function rows_active_by_col(){
		if($_POST){
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['IsActive'] = 1;
			$val = explode(',', $_POST['val']);
			$table=$_POST['table'];
			$col=$_POST['col'];
			$this->base_model->update_rows_by_col($col,$val,$table,$data);
			echo 1;
		}
	}
	public function rows_deactive_by_col(){
		if($_POST){
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['IsActive'] = 0;
			$val = explode(',', $_POST['val']);
			$table=$_POST['table'];
			$col=$_POST['col'];
			$this->base_model->update_rows_by_col($col,$val,$table,$data);
			echo 1;
		}
	}

	public function copy_product()
	{

		$id = $_POST['id'];
		$code = microtime(true) . rand(1111, 9999);
		$code2 = $_POST['code'];
		date_default_timezone_set("Asia/Tehran");
		$products = $this->base_model->get_data('products', '*', array('id' => $id));
		$product_attributes = $this->base_model->get_data('product_attributes', '*', array('code_p' => $code2));
		foreach ($products as $p) {
			$name=$p->name;
		}

		for ($j = 1; $j <1000; $j++) {
//			$name1 = $name . '-copy(' . $j . ')';
			$name1 = $name . '(' . $j . ')';
			$result = $code;

			if ($this->base_model->is_input_unique($result, 'code', 'products') &&
				$this->base_model->is_input_unique($name1, 'name', 'products')) {

				foreach ($products as $p) {
					$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
					$data['code'] = $result;
					$data['name'] = $name1;
					$data['id_cat1'] = $p->id_cat1;
					$data['id_cat2'] = $p->id_cat2;
					$data['text'] = $p->text;
					$data['isActive'] = $p->isActive;
					$this->base_model->insert('products', $data);
					echo 1;
				}
				foreach ($product_attributes as $pa){
					$data2['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
					$data2['code_p'] = $result;
					$data2['model']= $pa->model;
					$data2['jens'] = $pa->jens;
					$data2['size'] = $pa->size;
					$data2['color'] = $pa->color;
					$data2['brand'] = $pa->brand;
					$data2['price'] = $pa->price;
					$data2['off_percent'] = $pa->off_percent;
					$data2['supply'] = $pa->supply;
					$data2['isActive'] = $pa->isActive;
					$data2['default'] = $pa->default;
					$this->base_model->insert('product_attributes' , $data2);
				}

				break;

			}

		}

	}

	public function rules()
	{
		$data['title']='قوانین و مقررات';
		$data['rules']=$this->base_model->get_data('rules','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/rules');
	}
	public function insert_rules()
	{
		if ($_POST) {
			$data['text'] = $_POST['text1'];
			$this->base_model->insert('rules', $data);
			echo 1;
		}
	}
	public function edit_rules()
	{
		if($_POST){
			$id=$_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['text']=$_POST['text2'];
			$this->base_model->update('rules',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function footer()
	{
		$data['title']='فوتر';
		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/footer');
	}
	public function edit_call_us()
	{
		if ($_POST) {
			$id = $_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['mobile'] = $_POST['mobile'];
			$data['phone'] = $_POST['phone'];
			$data['address'] = $_POST['address'];
			$data['postal_code'] = $_POST['postal_code'];
			$data['email'] = $_POST['email'];
			$data['text'] = $_POST['text'];
			$this->base_model->update('call_us', array('id'=>$id), $data);
			echo 1;
		}
	}
	public function edit_links()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$data['link1'] = $_POST['link1'];
			$data['link2'] = $_POST['link2'];
			$data['link3'] = $_POST['link3'];
			$data['link4'] = $_POST['link4'];
			$data['link5'] = $_POST['link5'];
			$data['link6'] = $_POST['link6'];
			$data['link7'] = $_POST['link7'];
			$data['link8'] = $_POST['link8'];
			$this->base_model->update('links', array('id'=>$id), $data);
			echo 1;
		}
	}
	public function edit_socials()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$data['whatsapp'] = $_POST['whatsapp'];
			$data['facebook'] = $_POST['facebook'];
			$data['telegram'] = $_POST['telegram'];
			$data['instagram'] = $_POST['instagram'];
			$this->base_model->update('socials', array('id'=>$id), $data);
			echo 1;
		}
	}

	public function off_code()
	{
		$data['title']='کد تخفیف';
		$data['off_code']=$this->base_model->get_data('off_code','*');
		$data['off_code_products']=$this->base_model->get_data('off_code_products','*');
		$data['off_code_users']=$this->base_model->get_data('off_code_users','*');
		$data['products']=$this->base_model->get_data('products','*');
		$data['register']=$this->base_model->get_data('register','*');
		$data['profile']=$this->base_model->get_data('profile','*');


		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/off-code');
	}
	public function off_code_form()
	{
		$data['title']='افزودن کد تخفیف';
		$data['off_code']=$this->base_model->get_data('off_code','*');
		$data['register']=$this->base_model->get_data('register','*');
		$data['profile']=$this->base_model->get_data('profile','*');
		$data['products']=$this->base_model->get_data('products','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/insert_off_code');
	}
	public function get_code($id)
	{


			$off_code=$this->base_model->get_data('off_code','code',array('id'=>$id));
			if (isset($off_code[0])){
				echo $off_code[0]->code;
			}
//			foreach ($off_code as $off){
//				echo $off->code;
//				break;
//			}



	}
	function search_code()
	{
		$output = '';
		$query = '';
		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}

		$data = $this->base_model->search_off_code($query);


		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
			{
				$output .= ' <a
				class="tag" id="tag_'.$row->id.'" id_c="'.$row->id.'" style="display: block;">'.$row->code.'</a> ';

			}
		} else {
			$output = 0;
		}
		echo $output;
	}
	function off_code_list(){
			$fetch_data = $this->base_model->off_code_make_datatables();
			$data = [];

			foreach($fetch_data as $row)
			{

				if($row->end_date < date('Y-m-d') . ' ' . date('H:i:s')){$color='red';}

				$sub_array = array();

				$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" value="'.$row->id.'" id_off="'.$row->id.'"></input> ';

				$sub_array[] = '<span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;
" title="'.$row->code.'">'.$row->code.'</span>';
				$sub_array[] = number_format($row->mablagh);
				$sub_array[] = '<span>'.$row->start_date.'</span>';
				$sub_array[] = $row->end_date;
					date_default_timezone_set('Asia/Tehran');
					$date_expire = $row->end_date;
					$date = new DateTime($date_expire);
					$x = date('Y-m-d') . ' ' . date('H:i:s');
					$now = new DateTime($x);
//					echo $x;
				$sub_array[] = '<span id="spanDate" style="color:'.($row->end_date < date('Y-m-d') . ' ' . date('H:i:s') ? "red" :  "green" ).'">'.($row->end_date < date('Y-m-d') . ' ' . date('H:i:s') ? "منقضی شده" :  $date->diff($now)->format("%a روز") ).'</span>';
				$sub_array[] = $row->created;
				$sub_array[] = $row->modified;
				if($row->isActive==0) {$sub_array[] = '<button type="button" id="active" id_off="'.$row->id.'" class="btn btn-primary btn-xs">فعالسازی</button>';}
				else if($row->isActive==1){$sub_array[] = '<button type="button" id="deactive" id_off="'.$row->id.'" class="btn btn-secondry btn-xs">غیرفعالسازی</button>';}
				$sub_array[] = '
			<button type="button" id="copy" id_off="'.$row->id.'" class="btn btn-info">
			<i class="fa fa-copy"></i>
			</button>
			';
				$sub_array[] = '<a href="'. base_url('admin/edit_off_code/').$row->id .'">
			<button type="button" id="edit" id_off="'.$row->id.'" class="btn btn-warning">
			<i class="fa fa-edit" ></i>
			</button>
			</a>';
				$sub_array[] = '<button type="button" id="delete" id_off="'.$row->id.'" class="btn btn-danger">
			<i class="fa fa-trash" ></i>
			</button>';
				$data[] = $sub_array;
				$color = '';
			}
			$output = array(
				"draw"                    =>     intval($_POST["draw"]),
				"recordsTotal"          =>      $this->base_model->off_code_get_all_data(),
				"recordsFiltered"     =>     $this->base_model->off_code_get_filtered_data(),
				"data"                    =>     $data,
			);
			echo json_encode($output);
	}

	public function insert_off_code()
	{
		if ($_POST) {
			$this->load->library('form_validation');
			$this->load->helper('form');
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['code'] = $_POST['code'];
			$code = $_POST['code'];
			$data['mablagh'] = $_POST['mablagh'];
			$data['start_date'] = $_POST['datetime1'];
			$data['end_date'] = $_POST['datetime2'];
			$data['min_price'] = $_POST['min_price'];
			$data['uses'] = $_POST['uses'];
			$data['isActive'] = '1';

			$this->form_validation->set_message('required', 'فیلد الزامی');
			$this->form_validation->set_message('is_unique[off_code.code]', 'این مقدار تکراری است');
			$this->form_validation->set_message('greater_than', '%s باید بیشتر از %d (مبلغ تخفیف) باشد');

			$this->form_validation->set_rules('code', 'کد تخفیف', 'trim|required|is_unique[off_code.code]');
			$this->form_validation->set_rules('mablagh', 'مبلغ تخفیف', 'trim|required');
			$this->form_validation->set_rules('start_date', 'تاریخ شروع', 'trim|required');
			$this->form_validation->set_rules('end_date', 'تاریخ پایان', 'trim|required');
			$this->form_validation->set_rules('uses', 'تعداد دفعات استفاده', 'trim|required');
			$this->form_validation->set_rules('min_price', 'حداقل خرید', 'trim|required|greater_than['.$_POST['mablagh'].']');

			if ($this->form_validation->run()) {
				if ($_POST) {
					date_default_timezone_set("Asia/Tehran");
					$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
					$data['code'] = $_POST['code'];
					$code = $_POST['code'];
					$data['mablagh'] = $_POST['mablagh'];
					$data['start_date'] = $_POST['datetime1'];
					$data['end_date'] = $_POST['datetime2'];
					$data['min_price'] = $_POST['min_price'];
					$data['uses'] = $_POST['uses'];
					$data['isActive'] = '1';
					$this->base_model->insert('off_code', $data);
					$off_code = $this->base_model->get_data('off_code', 'id', array('code' => $code));
					if (isset($off_code[0])) {
						$id = $off_code[0]->id;
					}

					if (!(isset($_POST['products'])) ){
						$data2['id_product'] = 'all';
						$data2['id_off'] = $id;
						$data2['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
						$this->base_model->insert('off_code_products', $data2);
					}else {
						foreach ($_POST['products'] as $x) {
							$data2['id_product'] = $x;
							$data2['id_off'] = $id;
							$data2['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
							$this->base_model->insert('off_code_products', $data2);
						}
					}

					if (!(isset($_POST['users'])) ){
						$data3['id_user'] = 'all';
						$data3['id_off'] = $id;
						$data3['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
						$this->base_model->insert('off_code_users', $data3);
					}else {
						foreach ($_POST['users'] as $xx) {
							$data3['id_user'] = $xx;
							$data3['id_off'] = $id;
							$data3['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
							$this->base_model->insert('off_code_users', $data3);
						}
					}

//					redirect('admin/off_code_form');
					redirect('admin/off_code');
				}

			} else {
				$this->off_code_form();
			}
		}
	}

	function off_code_validation2(){
		$id=$_POST['id'];
		$data['code'] = $_POST['code'];
		$data['mablagh'] = $_POST['mablagh'];
		$off_code=$this->base_model->get_data('off_code','code',array('id'=>$id));
		if (isset($off_code[0])){
			$x = $off_code[0]->code;
		}
//		foreach ($off_code as $off){
//			$x=$off->code;
//			break;
//		}
		if($_POST['code'] != $x) {
			$this->form_validation->set_rules('code', 'off_code', 'required|trim|is_unique[off_code.code]');
			if($this->form_validation->run()){
				echo 1;
			}else{ echo 2;}

		} else {
			$this->form_validation->set_rules('code', 'off_code', 'required|trim');
		}

		$this->form_validation->set_rules('mablagh', 'off_code', 'required|trim|greater_than[0]');
		if($this->form_validation->run())
		{
			echo 1;
		}else{
			if($_POST["code"]==''){
				echo 2;
			}
			if(!($_POST["mablagh"]>0) || $_POST["mablagh"]==''){
				echo 3;
			}
		}
	}
	function off_code_validation(){
		$data['code'] = $_POST['code'];
		$data['mablagh'] = $_POST['mablagh'];
		$this->form_validation->set_rules('code', 'off_code', 'required|trim|is_unique[off_code.code]');
		$this->form_validation->set_rules('mablagh', 'off_code', 'required|trim|greater_than[0]');
		if($this->form_validation->run())
		{
			echo 1;
		}else{
			if($this->base_model->is_code_available($_POST["code"]) || $_POST["code"]==''){
				echo 2;
			}
			if(!($_POST["mablagh"]>=0) || $_POST["mablagh"]==''){
				echo 3;
			}
		}
	}
	function check_mablagh()
	{

		if($_POST["mablagh"] >= 0)
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}
		else
		{
			echo '<label class="text-danger"></span> مقدار منفی</label>';
		}

	}
	function check_code()
	{

			if($this->base_model->is_code_available($_POST["code"]))
			{
				echo '<label class="text-danger"></span> در سیستم موجود است</label>';
			}
			else
			{
				echo '<label class="text-success"></span> قابل قبول است</label>';
			}

	}
	function check_code2()
	{

		if($this->base_model->check_value('off_code',array('code'=>$_POST["code"],'id!='=>$_POST["id_off"])) )
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}
		else
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}

	}
	public function edit_off_code($id)
	{
		$data['title']='ویرایش کد تخفیف';

		$data['off_code']=$this->base_model->get_data('off_code','*',array('id'=>$id));
		$data['off_code_products']=$this->base_model->get_data('off_code_products','*',array('id_off'=>$id));
		$data['off_code_users']=$this->base_model->get_data('off_code_users','*',array('id_off'=>$id));
		$data['products']=$this->base_model->get_data('products','*');
		$data['register']=$this->base_model->get_data('register','*');
		$data['profile']=$this->base_model->get_data('profile','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/edit-off-code');
	}
	public function edit_off(){

		if ($_POST) {
			$this->load->library('form_validation');
			$this->load->helper('form');
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$id_off = $_POST['id'];
			$data['code'] = $_POST['code'];
			$code = $_POST['code'];
			$data['mablagh'] = $_POST['mablagh'];
			$data['start_date'] = $_POST['datetime1'];
			$data['end_date'] = $_POST['datetime2'];
			$data['min_price'] = $_POST['min_price'];
			$data['uses'] = $_POST['uses'];
			$data['isActive'] = '1';

			$this->form_validation->set_message('required', 'فیلد الزامی');
			$this->form_validation->set_message('_edit_unique', 'در سیستم موجود است');
			$this->form_validation->set_message('greater_than', '%s باید بیشتر از %d (مبلغ تخفیف) باشد');

			$this->form_validation->set_rules('code', 'کد تخفیف', 'trim|required|callback__edit_unique['.$id_off.']');
			$this->form_validation->set_rules('mablagh', 'مبلغ تخفیف', 'trim|required');
			$this->form_validation->set_rules('start_date', 'تاریخ شروع', 'trim|required');
			$this->form_validation->set_rules('end_date', 'تاریخ پایان', 'trim|required');
			$this->form_validation->set_rules('uses', 'تعداد دفعات استفاده', 'trim|required');
			$this->form_validation->set_rules('min_price', 'حداقل خرید', 'trim|required|greater_than['.$_POST['mablagh'].']');

			if ($this->form_validation->run()) {
				if ($_POST) {
					date_default_timezone_set("Asia/Tehran");
					$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
					$id_off =  $_POST['id'];
					$data['code'] = $_POST['code'];
					$code = $_POST['code'];
					$data['mablagh'] = $_POST['mablagh'];
					$data['start_date'] = $_POST['datetime1'];
					$data['end_date'] = $_POST['datetime2'];
					$data['min_price'] = $_POST['min_price'];
					$data['uses'] = $_POST['uses'];
					$data['isActive'] = '1';
					$this->base_model->update('off_code',array('id'=>$id_off) ,$data);

					if($this->base_model->is_inputs_available($id_off, 'id_off', 'off_code_products')
					||$this->base_model->is_inputs_available('all', 'id_off', 'off_code_products')){
						$this->base_model->delete_rows_by_col('id_off',$id_off,'off_code_products');
					}
					if (!(isset($_POST['products'])) ){
						$data2['id_product'] = 'all';
						$data2['id_off'] = $id_off;
						$data2['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
						$this->base_model->insert('off_code_products' ,$data2);
					}else {
						foreach ($_POST['products'] as $x) {
							$data2['id_product'] = $x;
							$data2['id_off'] = $id_off;
							$data2['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
							$this->base_model->insert('off_code_products' ,$data2);
						}
					}
					if($this->base_model->is_inputs_available($id_off, 'id_off', 'off_code_users')
					||$this->base_model->is_inputs_available($id_off, 'id_off', 'off_code_users')){
						$this->base_model->delete_rows_by_col('id_off',$id_off,'off_code_users');
					}
					if (!(isset($_POST['users'])) ){
						$data3['id_user'] = 'all';
						$data3['id_off'] = $id_off;
						$data3['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
						$this->base_model->insert('off_code_users' ,$data3);
					}else {
						foreach ($_POST['users'] as $xx) {
							$data3['id_user'] = $xx;
							$data3['id_off'] = $id_off;
							$data3['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
							$this->base_model->insert('off_code_users' ,$data3);
						}
					}

					redirect('admin/edit_off_code/'.$id_off);

				}

			} else {
				$this->edit_off_code($id_off);
			}
		}

	}

	function _edit_unique($x,$y)
	{
		$return_value = $this->base_model->check_value('off_code',array('code'=>$x,'id!='=>$y));
		if ($return_value)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function edit_multi_off(){
		if ($_POST) {
			$id = explode(',', $_POST['id']);
			$id_off = $id;
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			if($_POST['mablagh']!=''){
				$data['mablagh'] = $_POST['mablagh'];}
			if($_POST['start_date']!=''){
				$data['start_date'] = $_POST['datetime1'];}
			if($_POST['end_date']!=''){
				$data['end_date'] = $_POST['datetime2'];}
			if($_POST['min_price']!=''){
				$data['min_price'] = $_POST['min_price'];}
			if($_POST['uses']!=''){
				$data['uses'] = $_POST['uses'];}
			$this->base_model->update_rows_by_col('id',$id,'off_code',$data);

			if($this->base_model->is_inputs_available($id_off, 'id_off', 'off_code_products')){
				$this->base_model->delete_rows_by_col('id_off',$id_off,'off_code_products');
			}

			foreach ($_POST['products'] as $x){
				if($x!=''){
					$data2['id_product'] = $x;
					$data2['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');

					for ($i=0;$i<count($id);$i++){
						$data2['id_off'] = $id[$i];
					$this->base_model->insert('off_code_products',$data2);}

				}
			}

			if ($this->base_model->is_inputs_available($id_off, 'id_off', 'off_code_users')) {
				$this->base_model->delete_rows_by_col('id_off', $id_off, 'off_code_users');}

			foreach ($_POST['users'] as $xx) {
				if ($xx != '') {
					$data3['id_user'] = $xx;
					$data3['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');

					for ($i=0;$i<count($id);$i++){
						$data3['id_off'] = $id[$i];
						$this->base_model->insert('off_code_users',$data3);}

				}
			}

		redirect('admin/off_code/');
		}


	}

	public function check_orders()
	{
		if ($_POST) {
			$table = $_POST['table'];
			$column = $_POST['column'];
			$id = $_POST['id'];
			if($this->base_model->is_inputs_available($id, $column, $table))
			{
				echo 1;
			}
			else
			{
				echo 0;
			}
		}
	}
	public function check_orders_selected(){
		if (isset($_POST['ids'])) {
			$id = explode(',', $_POST['ids']);
			$orders = $this->base_model->get_datas('shopping_cart_order', '*', 'off_code', $id, NULL, NULL, NULL, NULL, NULL, 'order_code');
		}
		$i = 0;
		if(isset($orders[0])){foreach ($orders as $ord){
			$data['ord'] = ++$i;
			$data['off'][$i] = $ord->off_code;
		}}else{
			$data['ord'] = 0;
		}
		echo json_encode($data);
	}

	public function delete_off()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$this->base_model->delete_row('off_code', 'id', $id);
			echo 1;
		}
	}

	public function copy_off_code()
	{

		$id = $_POST['id'];
		date_default_timezone_set("Asia/Tehran");
		$off_code = $this->base_model->get_data('off_code', '*', array('id' => $id));

		if (isset($off_code[0])){
			$code = $off_code[0]->code;
		}

//		foreach ($off_code as $off) {
//			$code=$off->code;
//		}

		for ($j = 1; $j <1000; $j++) {
//			$code1 = $code . '-copy(' . $j . ')';
			$code1 = $code . '(' . $j . ')';

			if ($this->base_model->is_input_unique($code1, 'code', 'off_code')) {

				foreach ($off_code as $off) {
					$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
					$data['code'] = $code1;
					$data['mablagh'] = $off->mablagh;
					$data['start_date'] = $off->start_date;
					$data['end_date'] = $off->end_date;
					$data['isActive'] = $off->isActive;
					$this->base_model->insert('off_code', $data);
					echo 1;
				}

				break;

			}

		}

	}
	public function delete_off_codes() {
		if (isset($_POST['ids'])) {
			$ids = explode(',', $_POST['ids']);
//			$id_off = $ids;
			$results = $this->base_model->delete_rows_by_ids($ids,'off_code');
//			$results2 = $this->base_model->delete_rows_by_ids($id_off,'shopping_cart');

			if ($results === TRUE) {
				echo '<span style="color:green;">row(s) successfully deleted</span>';
			} else {
				echo '<span style="color:red;">Something went wrong during row deletion</span>';
			}
		} else {
			echo '<span style="color:red;">You must select at least one row for deletion</span>';
		}
	}

	public function update_off_checkall(){
		if ($_POST) {
			$id = explode(',', $_POST['id']);
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			if($_POST['mablagh']!=''){
				$data['mablagh'] = $_POST['mablagh'];}
			if($_POST['start_date']!=''){
				$data['start_date'] = $_POST['start_date'];}
			if($_POST['end_date']!=''){
				$data['end_date'] = $_POST['end_date'];}

			$this->base_model->update_rows_by_col('id',$id,'off_code',$data);

//			if($this->base_model->is_input_available($id, 'id_p', 'shopping_cart')){
//				$this->base_model->update_rows_by_col('id_p',$id,'shopping_cart',$data2);
//				echo 2;
//			}else{
//				echo 3;
//			}


			echo 1;
		}
	}

	public function comments(){
		$data['title']='نظرات کاربر';
		$data['comment']=$this->base_model->get_data('comments','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/comments2');
	}
	public function comments_product($code){
		$data['title']='نظرات محصول';
		$data['code']=$code;
		$data['comments']=$this->base_model->get_data('comments','*',array('code'=>$code));
		$data['comments2']=$this->base_model->get_data('comments','*',array('code'=>$code));
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/comments_product');
	}
	public function comments_user($uid){
		$data['title']='سوایق نظرات کاربر';
		$data['uid']=$uid;

		$data['profile']=$this->base_model->get_data('profile','*',array('user_id'=>$uid));

		$data['comments1']=$this->base_model->get_data('comments','*');
		$data['comments2']=$this->base_model->get_data('comments','*');


		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/comments_user');
	}

	function comments_list()
	{
		$fetch_data = $this->base_model->comments_make_datatables();
		$id = [];
		$data = [];
		$color = '';

		$profile=$this->base_model->get_data('profile','*');


		foreach($fetch_data as $row)
		{

			foreach($profile as $row1)
			{
				if($row->uid==$row1->user_id){
					$href=base_url('admin/comments_user/') .$row->uid;
				}
				if ($row->uid==''){
					$href="";
				}
			}


			if($row->role==0){$color='red';}

			$sub_array = [];
			$id[] = $color;


			$sub_array[] = '';
			$sub_array[] = '<a href="'. base_url('admin/comments_product/').$row->pcode .'"><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" title="'.$row->pname.'" >'.$row->pname.'</span></a>';
			$sub_array[] = '<a href='.($href=="" ? "" : $href).'><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" id="name_' . $row->id . '" title="'.$row->name.'" >'.$row->name.'</span></a>';
			$sub_array[] = '<span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" id="text_' . $row->id . '" title="'.$row->text.'" >'.$row->text.'</span>';
			$sub_array[] = '<span style="" id_cmnt="' . $row->id . '">'.$row->created.'</span>';
			$sub_array[] = '<span style="" id_cmnt="' . $row->id . '">'.$row->modified.'</span>';
			$sub_array[] = '<span style="" id_cmnt="' . $row->id . '">'.$row->cond.'</span>';

			$sub_array[] =
				'<button style="display:'.($row->role ==0 || $row->role ==2 ? "inline" : "none").'" id="accept" id_cmnt="' . $row->id . '" class="btn btn-success btn-xs">تایید</button>
				<button style="display:'.($row->role ==0 || $row->role ==1 ? "inline" : "none").'" id="reject" id_cmnt="' . $row->id . '" class="btn btn-primary btn-xs">رد</button>';
			$sub_array[] = '<a href="'. base_url('admin/edit_comments/').$row->id.'/'.$row->code .'">
			<button type="button" id="edit" id_cmnt="' . $row->id . '" class="btn btn-warning">
			<i class="fa fa-edit" ></i>
			</button>
			</a>';
			$sub_array[] = '<button id="delete" id_cmnt="' . $row->id . '" class="btn btn-danger">
<i class="fa fa-trash"></i>
</button>';

			$data[] = $sub_array;
			$color = '';

		}
		$output = [
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->comments_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->comments_get_filtered_data(),
			"data"                    =>     $data,
			"id"                    =>     $id,
		];

		echo json_encode($output);

	}
	function comments_list2()
	{
		$output = '';
		$query = '';
		if($_POST)
		{
			$query = $this->input->post('query');
		}
		$data = $this->base_model->search_comment($query);
		$data2= $this->base_model->get_data('products','*');

		$output .= '
  <div class="table-responsive">
     <table class="table table-bordered">
     			<tr>
					<th>نام محصول</th>
					<th>نام کاربر</th>
					<th>نظر</th>
					<th >تاریخ</th>
					<th >وضعیت</th>
					<th style="text-align: center">عملیات</th>
				</tr>
  ';
		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row){ foreach ($data2 as $row2) {if ($row->code==$row2->code){
				if ($row->role ==1){
				$output .= '
            			<tr id="cmnt_' . $row->id . '"
						style="background-color: #fff;">
						<td>' . $row2->name . '</td>
						<td><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;"  
						id="name_' . $row->id . '" >' . $row->name . '</span></td>
						<td><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;"   
						id="text_' . $row->id . '">' . $row->text . '</span></td>
						<td><span id="date_' . $row->id . '" >' . $row->date . '</span></td>
						<td><span id="con_' . $row->id . '" >' . $row->cond . '</span></td>
						<td style="text-align: center">
							
							<button id="reject" id_cmnt="' . $row->id . '" class="btn btn-warning">رد</button>
							
							<button style="margin-right: if (' . $row->role . '==1){ echo 57px;}"
									class="btn btn-info" id="edit_1" id_cmnt="' . $row->id . '" >
								نمایش	 </button>
							<button id="delete" id_cmnt="' . $row->id . '" class="btn btn-danger">حذف</button>
						</td>
						</tr>
              ';

			}
			else if ($row->role ==0){
				$output .= '
            			<tr id="cmnt_' . $row->id . '"
						style="background-color: #fceeee;">
						<td>' . $row2->name . '</td>
						<td><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;"  
						id="name_' . $row->id . '" >' . $row->name . '</span></td>
						<td><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;"   
						id="text_' . $row->id . '">' . $row->text . '</span></td>
						<td><span id="date_' . $row->id . '" >' . $row->date . '</span></td>
						<td><span id="con_' . $row->id . '" >' . $row->cond . '</span></td>
						<td style="text-align: center">
							<div style="display: inline;">
								<button id="accept" id_cmnt="' . $row->id . '" class="btn btn-success">تایید</button>
								<button id="reject" id_cmnt="' . $row->id . '" class="btn btn-warning">رد</button>
							</div>
							<button
									class="btn btn-info" id="edit_1" id_cmnt="' . $row->id . '" >
								نمایش	 </button>
							<button id="delete" id_cmnt="' . $row->id . '" class="btn btn-danger">حذف</button>
						</td>
						</tr>
              ';
			}
			else if ($row->role ==2){
					$output .= '
            			<tr id="cmnt_' . $row->id . '"
						style="background-color: #fff;">
						<td>' . $row2->name . '</td>
						<td><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;"  
						id="name_' . $row->id . '" >' . $row->name . '</span></td>
						<td><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;"   
						id="text_' . $row->id . '">' . $row->text . '</span></td>
						<td><span id="date_' . $row->id . '" >' . $row->date . '</span></td>
						<td><span id="con_' . $row->id . '" >' . $row->cond . '</span></td>
						<td style="text-align: center">
							
							<button id="accept" id_cmnt="' . $row->id . '" class="btn btn-success">تایید</button>
							
							<button style="margin-right: if (' . $row->role . '==1){ echo 57px;}"
									class="btn btn-info" id="edit_1" id_cmnt="' . $row->id . '" >
								نمایش	 </button>
							<button id="delete" id_cmnt="' . $row->id . '" class="btn btn-danger">حذف</button>
						</td>
						</tr>
              ';

				}
			}}}
		}
		else
		{
			$output .= '<tr>
       <td colspan="5">موردی یافت نشد</td>
      </tr>';
		}
		$output .= '</table>';
		echo $output;
	}
	public function reject_comment()
	{
		if($_POST){
			$id=$_POST['id'];
			$data['role']='2';
			$data['cond']='رد شده';
			$this->base_model->update('comments',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function accept_comment()
	{
		if($_POST){
			$id=$_POST['id'];
			$data['role']='1';
			$data['cond']='تایید شده';
			$this->base_model->update('comments',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function delete_comment()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$this->base_model->delete_row('comments', 'id', $id);
			echo 1;
		}
	}
	public function edit_comments($id,$code){
		$data['title']='نظرات کاربر';
		$data['code']=$code;
		$data['id']=$id;
		$data['products'] = $this->base_model->get_data('products', '*', array('code' => $code));
		if($this->session->userdata('user_id')) {
			$data['profile'] = $this->base_model->get_data('profile', '*', array('user_id' => $_SESSION['user_id']));}


		$comments1=$this->base_model->get_data('comments','*',array('id'=>$id));
		$data['parent_id']=$comments1[0]->parent_id;
		$parent_id=$comments1[0]->parent_id;

		if($parent_id==0){
			$data['comments1']=$this->base_model->get_data('comments','*',array('id'=>$id));
			$data['comments2']=$this->base_model->get_data('comments','*',array('parent_id'=>$id));
		}else{
			$data['comments1']=$this->base_model->get_data('comments','*',array('id'=>$parent_id,'id!='=>$id));
			$data['comments2']=$this->base_model->get_data('comments','*',array('parent_id'=>$parent_id));
		}


		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/edit_comments');
	}
	public function edit_cm()
	{
		if($_POST){
			$id=$_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name']=$_POST['name'];
			$data['text']=$_POST['text'];
			$this->base_model->update('comments',array('id'=>$id) ,$data);
			echo 1;
		}
	}

	public function sales()
	{
		$data['title']='اطلاعات فروش';
		$data['info_fact']=$this->base_model->get_data('info_fact','*');
		$data['profile']=$this->base_model->get_data('profile','*');
		$data['shopping_cart_order']=$this->base_model->get_data('shopping_cart_order','*',NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'order_code');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/sales');
	}
	public function sales_list2(){
		$fetch_data = $this->base_model->ord_make_datatables();
		$data = [];

		foreach($fetch_data as $row)
		{

			$sub_array = array();

			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" value="'.$row->id.'" id_ord="'.$row->id.'"></input> ';
			$sub_array[] = '
			<a href="'. base_url('admin/sale_datail/').$row->info_id.'/'.$row->order_code.'">
			<span id="ord_' . $row->id . '">'.$row->order_code.'</span>
			</a>';
			$sub_array[] = '
			<a href="'. base_url('admin/user_detail/').$row->prof_id.'">
			<span id="ord_' . $row->id . '">'.$row->name.' '.$row->family.'</span>
			</a>';
			$sub_array[] = '<span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;
" title="'.$row->payment.'">'.number_format($row->payment).'</span>';
			$sub_array[] = $row->status;
			date_default_timezone_set('Asia/Tehran');
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->isActive==0) {$sub_array[] = '<button type="button" id="active" id_ord="'.$row->id.'" class="btn btn-primary btn-xs">فعالسازی</button>';}
			else if($row->isActive==1){$sub_array[] = '<button type="button" id="deactive" id_ord="'.$row->id.'" class="btn btn-secondry btn-xs">غیرفعالسازی</button>';}
			$sub_array[] = '
			<button type="button" id="copy" id_ord="'.$row->id.'" code_ord="'.$row->order_code.'" class="btn btn-info">
			<i class="fa fa-copy"></i>
			</button>
			';

			$sub_array[] = '<a href="'.  base_url('admin/edit_order/').$row->info_id.'/'.$row->order_code .'">
			<button type="button" id="edit" id_ord="'.$row->id.'" class="btn btn-warning">
			<i class="fa fa-edit" ></i>
			</button>
			</a>';
			$sub_array[] = '<button type="button" id="delete" id_ord="'.$row->id.'" class="btn btn-danger">
			<i class="fa fa-trash" ></i>
			</button>';
			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->ord_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->ord_get_filtered_data(),
			"data"                    =>     $data,
		);
		echo json_encode($output);
	}
	public function sales_list(){
		$fetch_data = $this->base_model->ord_make_datatables();
		$data = [];

		foreach($fetch_data as $row)
		{

			$sub_array = array();

			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" value="'.$row->order_code.'" code_ord="'.$row->order_code.'"></input> ';
			$sub_array[] = '
			<a href="'. base_url('admin/sale_datail/').$row->order_code.'">
			<span id="ord_' . $row->order_code . '">'.$row->order_code.'</span>
			</a>';
			$sub_array[] = '
			<a href="'. base_url('admin/edit_user/').$row->id_user_ord.'">
			<span id="ord_' . $row->order_code . '">'.$row->name.' '.$row->family.'</span>
			</a>';
			$sub_array[] = '<span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;
" title="'.$row->payment.'">'.number_format($row->payment).'</span>';
			$sub_array[] = $row->status;
			date_default_timezone_set('Asia/Tehran');
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->isActive==0) {$sub_array[] = '<button type="button" id="active" code_ord="'.$row->order_code.'" class="btn btn-primary btn-xs">فعالسازی</button>';}
			else if($row->isActive==1){$sub_array[] = '<button type="button" id="deactive" code_ord="'.$row->order_code.'" class="btn btn-secondry btn-xs">غیرفعالسازی</button>';}
			$sub_array[] = '
			<button type="button" id="copy" code_ord="'.$row->order_code.'" class="btn btn-info">
			<i class="fa fa-copy"></i>
			</button>
			';

			$sub_array[] = '<a href="'.  base_url('admin/edit_order/').'/'.$row->order_code .'">
			<button type="button" id="edit" code_ord="'.$row->order_code.'" class="btn btn-warning">
			<i class="fa fa-edit" ></i>
			</button>
			</a>';
			$sub_array[] = '<button type="button" id="delete" code_ord="'.$row->order_code.'" class="btn btn-danger">
			<i class="fa fa-trash" ></i>
			</button>';
			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->ord_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->ord_get_filtered_data(),
			"data"                    =>     $data,
		);
		echo json_encode($output);
	}

	public function insert_factor(){
		$data['title'] = 'ثبت فاکتور جدید';
		$data['info_fact'] = $this->base_model->get_data('info_fact','*');
		$data['shopping_cart_order']=$this->base_model->get_data('shopping_cart_order','*');
		$data['products'] = $this->base_model->get_data('products', '*');
		$data['off_code'] = $this->base_model->get_data('off_code', '*');
		$data['model'] = $this->base_model->get_data('model', '*');
		$data['size'] = $this->base_model->get_data('size', '*');
		$data['jens'] = $this->base_model->get_data('jens', '*');
		$data['brand'] = $this->base_model->get_data('brand', '*');
		$data['color'] = $this->base_model->get_data('color', '*');
		$data['profile']=$this->base_model->get_data('profile','*');
		$data['register']=$this->base_model->get_data('register','*');
		$data['product_attributes']=$this->base_model->get_data('product_attributes','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/insert_factor',$data);
	}
	public function insert_fact() {
		$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
		$data['id_attr'] = $_POST['id_attr'];
		$data['qty'] = $_POST['qty'];
		$data['price'] = $_POST['price'];
		$data['total_price1'] = $_POST['total_price1'];
		$data['off_percent'] = $_POST['off_percent'];
		$data['off_price'] = $_POST['off_price'];
		$data['total_price'] = $_POST['total_price'];
		$data['id_p'] = $_POST['id_p'];
		$data['name'] = $_POST['name'];
		$data['code_p'] = $_POST['code_p'];
		$data['final_price'] = $_POST['final_price'];
		$data['payment'] = $_POST['payment'];
		$data['order_code'] = $_POST['order_code'];
		$data['status'] = $_POST['status'];
		$data['user_id'] = $_POST['user_id'];
		$data['off_code'] = $_POST['off_code'];
		$data['off_code_mablagh'] = $_POST['off_code_mablagh'];
		$data['isActive'] = '1';
		echo $this->base_model->insert('shopping_cart_order', $data);
	}

	public function edit_order( $id2 ){
		$data['title'] = 'ویرایش فاکتور';
		$data['order_code'] = $id2;
		$data['info_fact'] = $this->base_model->get_data('info_fact','*',array('ord_code'=>$id2));
		$data['shopping_cart_order']=$this->base_model->get_data('shopping_cart_order','*',array('order_code'=>$id2),NULL,NULL,NULL,'created DESC');
		$data['products'] = $this->base_model->get_data('products', '*');
		$data['off_code'] = $this->base_model->get_data('off_code', '*');
		$data['model'] = $this->base_model->get_data('model', '*');
		$data['size'] = $this->base_model->get_data('size', '*');
		$data['jens'] = $this->base_model->get_data('jens', '*');
		$data['brand'] = $this->base_model->get_data('brand', '*');
		$data['color'] = $this->base_model->get_data('color', '*');
		$data['profile']=$this->base_model->get_data('profile','*');
		$data['register']=$this->base_model->get_data('register','*');
		$data['product_attributes']=$this->base_model->get_data('product_attributes','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/edit_order',$data);
	}

	public function add_ord() {
		$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
		$data['id_attr'] = $_POST['id_attr'];
		$data['qty'] = $_POST['qty'];
		$data['price'] = $_POST['price'];
		$data['total_price1'] = $_POST['total_price1'];
		$data['off_percent'] = $_POST['off_percent'];
		$data['off_price'] = $_POST['off_price'];
		$data['total_price'] = $_POST['total_price'];
		$data['id_p'] = $_POST['id_p'];
		$data['name'] = $_POST['name'];
		$data['code_p'] = $_POST['code_p'];
		$data['final_price'] = $_POST['final_price'];
		$data['payment'] = $_POST['payment'];
		$data['order_code'] = $_POST['order_code'];
		$data['status'] = $_POST['status'];
		$data['user_id'] = $_POST['user_id'];
		$data['off_code'] = $_POST['off_code'];
		$data['off_code_mablagh'] = $_POST['off_code_mablagh'];
		$data['isActive'] = '1';
		echo $this->base_model->insert('shopping_cart_order', $data);
	}

	public function edit_ord() {
		$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
		$id = $_POST['id'];
		$data['id_attr'] = $_POST['id_attr'];
		$data['qty'] = $_POST['qty'];
		$data['price'] = $_POST['price'];
		$data['total_price1'] = $_POST['total_price1'];
		$data['off_percent'] = $_POST['off_percent'];
		$data['off_price'] = $_POST['off_price'];
		$data['total_price'] = $_POST['total_price'];
		$data['id_p'] = $_POST['id_p'];
		$data['name'] = $_POST['name'];
		$data['code_p'] = $_POST['code_p'];
		$data['final_price'] = $_POST['final_price'];
		$data['payment'] = $_POST['payment'];
		$data['order_code'] = $_POST['order_code'];
		$data['status'] = $_POST['status'];
		$data['user_id'] = $_POST['user_id'];
		$data['off_code'] = $_POST['off_code'];
		$data['off_code_mablagh'] = $_POST['off_code_mablagh'];
		$data['isActive'] = '1';
		echo $this->base_model->update('shopping_cart_order',array('id'=>$id), $data);
	}

	public function deleted_ord(){
		$ids = $_POST['id'];
		$order_code = $_POST['order_code'];
		$comma='';
		$ids2='';
		if (isset($_POST['id'])) {
			$ids = explode(',', $_POST['id']);
			$shopping_cart_order = $this->base_model->get_datas('shopping_cart_order', 'id','order_code',$order_code,'id', $ids);
			foreach ($shopping_cart_order as $ord){
				$ids2 = $ids2 . $comma . $ord->id;
				$comma=',';
				echo $ids2;
			}
			$ids3 = explode(',', $ids2);
			$results = $this->base_model->delete_rows_by_ids($ids3,'shopping_cart_order');

			if ($results === TRUE) {
				echo '<span style="color:green;">row(s) successfully deleted</span>';
			} else {
				echo '<span style="color:red;">Something went wrong during row deletion</span>';
			}


		} else {
			echo '<span style="color:red;">You must select at least one row for deletion</span>';
		}
	}

	public function get_product(){
		$id_attr = $_POST['id_attr'];
		$products = $this->base_model->get_data('products', '*');
		$product_attributes = $this->base_model->get_data('product_attributes', '*');
		$i=0;
		$output='';
		$order_code = $_POST['order_code'];
		$user_id = $_POST['user_id'];
		$status = $_POST['status'];
		$code_p = $_POST['code_p'];
		$id_p = $_POST['id_p'];

		if(isset($products[0])){
			foreach ($product_attributes as $pa){foreach ($products as $p){if($p->code == $pa->code_p){
			$data[$i]['name'] = $p->name;
			$data[$i]['code_p'] = $pa->code_p;
			$data[$i]['price'] = $pa->price;
			$data[$i]['off_percent'] = $pa->off_percent;
			$data[$i]['id_attr'] = $pa->id;
			$data[$i]['id_p'] = $p->id;
			$data[$i]['code_p'] = $p->code;
			$data[$i]['order_code'] = $order_code;
			$data[$i]['user_id'] = $user_id;
			$data[$i]['status'] = $status;
			$i++;
		}}}
		}
		for ($j=0;$j<count($data);$j++){
			$output .= '
		<option '.($data[$j]['id_attr']==$id_attr? 'selected':'').'
		value="'.$data[$j]['id_attr'].'"
		price="'.$data[$j]['price'].'"
		id_attr="'.$data[$j]['id_attr'].'"
		id_p="'.$data[$j]['id_p'].'"
		name="'.$data[$j]['name'].'"
		off_percent="'.$data[$j]['off_percent'].'"
		code_p="'.$data[$j]['code_p'].'"
		user_id="'.$data[$j]['user_id'].'"
		order_code="'.$data[$j]['order_code'].'"
		status="'.$data[$j]['status'].'"
		>
		'.$data[$j]['name'].' - '.$data[$j]['price'].' ()
		</option>
  				
			';
		}
		echo $output;
	}

	public function edit_insert_info(){
		if($_POST){
			$user_id = $_POST['user_id'];
			$ord_code = $_POST['order_code'];
			$rand_code = rand(000000000,999999999);;
			$info = $this->base_model->get_data('info_fact', '*',array('ord_code'=>$ord_code));
			if(isset($info[0])){
				date_default_timezone_set("Asia/Tehran");
				$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data['name'] = $_POST['name'];
				$data['address'] = $_POST['address'];
				$data['postal_code'] = $_POST['postal_code'];
				$data['phone_number'] = $_POST['phone_number'];
				$this->base_model->update('info_fact',array('ord_code'=>$ord_code) ,$data);
			}
			else{
				date_default_timezone_set("Asia/Tehran");
				$data2['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data2['ord_code'] = $ord_code;
				$data2['user_id'] = $user_id;
				$data2['name'] = $_POST['name'];
				$data2['address'] = $_POST['address'];
				$data2['postal_code'] = $_POST['postal_code'];
				$data2['phone_number'] = $_POST['phone_number'];
				$data2['random_code'] = $rand_code;
				$this->base_model->insert('info_fact',$data2);
			}
		}
	}

	public function get_profile(){
		$user_id = $_POST['user_id'];
		$profile = $this->base_model->get_data('profile', '*',array('user_id'=>$user_id));

		if(isset($profile[0])){
			$data['name'] = $profile[0]->name.' '.$profile[0]->family;
			$data['address'] = $profile[0]->address;
			$data['postal_code'] = $profile[0]->postal_code;
			$data['phone_number'] = $profile[0]->reciever_phone_number;
		}
		echo json_encode($data);

	}
	public function get_inventory(){
		$inv_from = $_POST['inv_from'];
		$inventory = $this->base_model->get_data('inventory', '*',array('id!='=>$inv_from));

		$result='<option value="0">انتخاب کنید..</option>';
		if(isset($inventory[0])){foreach ($inventory as $inv){
			$result=$result."<option value='$inv->id'>$inv->name</option>";
		}}
		echo $result;

	}
	public function get_inventory_supply(){
		$inv_from = $_POST['inv_from'];
		$inventory_supply = $this->base_model->get_data('inventory_supply', '*', array('id_inv'=>$inv_from, 'supply!='=>'0'));
		$a = array();
		if(isset($inventory_supply[0])){
			foreach ($inventory_supply as $inv_sup){
				$a[] = $inv_sup->id_attr;
			}
		}
		$products = $this->base_model->get_data('products', '*');
		$product_attributes = $this->base_model->get_datas('product_attributes', '*', 'id', $a);

		$model=$this->base_model->get_data('model','*');
		$jens=$this->base_model->get_data('jens','*');
		$brand=$this->base_model->get_data('brand','*');
		$color=$this->base_model->get_data('color','*');
		$size=$this->base_model->get_data('size','*');
		$result='<option value="0">انتخاب کنید..</option>';
		$x1='';
		$x2='';
		$x3='';
		$x4='';
		$x5='';
		$x6='';
		$x7='';
		foreach ($inventory_supply as $inv_sup){foreach ($product_attributes as $pa){foreach ($products as $p){if ($inv_sup->code_p==$p->code && $pa->code_p==$p->code){
			if($pa->model!=0){foreach ($model as $mo){if($pa->model==$mo->id){ $x1='مدل:'.$je->name.' ';}}}
			if($pa->jens!=0){foreach ($jens as $je){if($pa->jens==$je->id){ $x2='جنس:'.$je->name.' ';}}}
			if($pa->size!=0){foreach ($size as $si){if($pa->size==$si->id){ $x3='سایز:'.$si->name.' ';}}}
			if($pa->brand!=0){foreach ($brand as $br){if($pa->brand==$br->id){ $x4='برند:'.$br->name.' ';}}}
			if($pa->color!=0){foreach ($color as $co){if($pa->color==$co->id){ $x5='رنگ:'.$co->name.' ';}}}
			if($x1!=''||$x2!=''||$x3!=''||$x4!=''||$x5!=''){
				$x6='(';
				$x7=')';
			}
			$result.="<option value='$inv_sup->id_attr' unit='$pa->unit' supply='$inv_sup->supply' buy_price='$pa->buy_price' price='$pa->price'>$p->name - $pa->price $x6 $x1 $x2 $x3 $x4 $x5 $x7</option>";
		}}}}
		if($result=='<option value="0">انتخاب کنید..</option>'){
			$result.='<optgroup label="انبار مبدا موجودی ندارد.." style="background-color: yellow; text-align: center; font-family: Farnaz"></optgroup>';
		}
		$data['result']=$result;
		echo json_encode($data);
	}

	public function copy_order()
	{
		$code = $_POST['code'];
		date_default_timezone_set("Asia/Tehran");
		$order = $this->base_model->get_data('shopping_cart_order', '*', array('order_code' => $code));
		$info = $this->base_model->get_data('info_fact', '*', array('ord_code' => $code));

		$ord1=rand(000000000,999999999);

		if ($this->base_model->is_input_unique($ord1, 'order_code', 'shopping_cart_order')) {
			foreach ($order as $ord) {
				$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data['order_code'] = $ord1;

				$data['user_id'] = $ord->user_id;
				$data['id_p'] = $ord->id_p;
				$data['code_p'] = $ord->code_p;
				$data['id_attr'] = $ord->id_attr;
				$data['name'] = $ord->name;
				$data['price'] = $ord->price;
				$data['qty'] = $ord->qty;
				$data['off_percent'] = $ord->off_percent;
				$data['off_price'] = $ord->off_price;
				$data['total_price1'] = $ord->total_price1;
				$data['total_price'] = $ord->total_price;
				$data['final_price'] = $ord->final_price;
				$data['off_code'] = $ord->off_code;
				$data['payment'] = $ord->payment;
				$data['off_code_mablagh'] = $ord->off_code_mablagh;
				$data['status'] = 'در انتظار ارسال';

				$data['isActive'] = $ord->isActive;
				$this->base_model->insert('shopping_cart_order', $data);
			}


			foreach ($info as $inf) {
				date_default_timezone_set("Asia/Tehran");
				$data2['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data2['ord_code'] = $ord1;
				$data2['user_id'] = $inf->user_id;
				$data2['name'] = $inf->name;
				$data2['address'] = $inf->address;
				$data2['postal_code'] = $inf->postal_code;;
				$data2['phone_number'] = $inf->phone_number;
				$random_code = rand(000000000, 999999999);
				$data2['random_code'] = $random_code;
				$this->base_model->insert('info_fact', $data2);
			}
			echo 1;
		}


	}
	public function delete_ord()
	{
		if ($_POST) {
			if (isset($_POST['val'])) {
				$val = explode(',', $_POST['val']);
				$col = $_POST['col'];
				$table = $_POST['table'];
				$col2 = 'ord_code';
				$table2 = 'info_fact';

				$results = $this->base_model->delete_rows_by_col($col, $val,$table);

				if ($results === TRUE) {
					$results2 = $this->base_model->delete_rows_by_col($col2, $val,$table2);
					if ($results2 === TRUE) {
						echo '<span style="color:green;">row(s) successfully deleted</span>';
					}
				} else {
					echo '<span style="color:red;">Something went wrong during row deletion</span>';
				}
			} else {
				echo '<span style="color:red;">You must select at least one row for deletion</span>';
			}
		}
	}
	public function delete_orders() {
		if (isset($_POST['val'])) {
			$val = explode(',', $_POST['val']);
			$col = $_POST['col'];
			$table = $_POST['table'];
			$col2 = 'ord_code';
			$table2 = 'info_fact';

			$results = $this->base_model->delete_rows_by_col($col, $val,$table);

			if ($results === TRUE) {
				$results2 = $this->base_model->delete_rows_by_col($col2, $val,$table2);
				if ($results2 === TRUE) {
					echo '<span style="color:green;">row(s) successfully deleted</span>';
				}
			} else {
				echo '<span style="color:red;">Something went wrong during row deletion</span>';
			}
		} else {
			echo '<span style="color:red;">You must select at least one row for deletion</span>';
		}
	}
	public function user_detail($id){
		$data['title']='مشخصات دریافت کننده';
		$data['profile']=$this->base_model->get_data('profile','*',array('id'=>$id));
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/user_detail');
	}
	public function sale_datail($id2)
	{
		$data['title']='جزئیات فروش';
		$data['info_fact']=$this->base_model->get_data('info_fact','*',array('ord_code'=>$id2));
		$data['shopping_cart_order']=$this->base_model->get_data('shopping_cart_order','*',array('order_code'=>$id2));
		$data['products'] = $this->base_model->get_data('products', '*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/sale-detail');
	}
	public function delete_sale()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$order_code = $_POST['order_code'];
			$this->base_model->delete_row('info_fact', 'id', $id);
			$this->base_model->delete_row('shopping_cart_order', 'order_code', $order_code);
			echo 1;
		}
	}
	function upload_img2()
	{
		//	sleep(3);
		$code = $_POST['code'];
		if($_FILES["files"]["name"] != '')
		{
			if (!is_dir('uploads/product/'.$code)){
				mkdir('uploads/product/'.$code, 0777, TRUE);
			}
			$output = '';
			$config["upload_path"] = 'uploads/product/'.$code.'/';
			$config["allowed_types"] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			for($count = 0; $count<count($_FILES["files"]["name"]); $count++)
			{
				$_FILES["file"]["name"] = $_FILES["files"]["name"][$count];
				$_FILES["file"]["type"] = $_FILES["files"]["type"][$count];
				$_FILES["file"]["tmp_name"] = $_FILES["files"]["tmp_name"][$count];
				$_FILES["file"]["error"] = $_FILES["files"]["error"][$count];
				$_FILES["file"]["size"] = $_FILES["files"]["size"][$count];

				if($this->upload->do_upload('file'))
				{
					$data = $this->upload->data();
					$uploadData[$count]['direction']='uploads/product/'.$code.'/'.$data['file_name'];
					$uploadData[$count]['role']='product';
					$uploadData[$count]['user_id']=$code;

					foreach($uploadData as $row1) {
						$img_id = $this->base_model->insert('images', $row1);
						$output .= '
  			<div id="show_image_' . $img_id . '" style="border: 1px solid rebeccapurple;border-radius: 10px;padding: 5px;width: 113px;height: 140px;text-align: center;margin:0 0 10px 10px;display: inline-block;overflow:auto">
			<span id="delete_image" id_img="' . $img_id . '" style="color: red;cursor: pointer;margin-bottom: 5px;">حذف</span>
			<img style="width: 100px;height: 100px;" src="' . base_url() . 'uploads/product/' . $code . '/' . $data["file_name"] . '">
			</div>	
			';
					}
				}
			}
			echo $output;
		}
	}

	function upload_img(){
		$code = $_POST['code'];
		$data = array();
		$errorUploadType = $statusMsg = '';

			// If files are selected to upload
			if(!empty($_FILES['files']['name']) && count(array_filter($_FILES['files']['name'])) > 0){
				$filesCount = count($_FILES['files']['name']);
				for($i = 0; $i < $filesCount; $i++){
					$_FILES['file']['name']     = $_FILES['files']['name'][$i];
					$_FILES['file']['type']     = $_FILES['files']['type'][$i];
					$_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
					$_FILES['file']['error']     = $_FILES['files']['error'][$i];
					$_FILES['file']['size']     = $_FILES['files']['size'][$i];

					// File upload configuration
					if (!is_dir('uploads/product/'.$code)){
						mkdir('uploads/product/'.$code, 0777, TRUE);
					}
					$output = '';
					$config["upload_path"] = 'uploads/product/'.$code.'/';
					$config["allowed_types"] = 'gif|jpg|png|jpeg';
					//$config['max_size']    = '100';
					//$config['max_width'] = '1024';
					//$config['max_height'] = '768';

					// Load and initialize upload library
					$this->load->library('upload', $config);
					$this->upload->initialize($config);

					// Upload file to server

					if($this->upload->do_upload('file')){
						// Uploaded file data
						$fileData = $this->upload->data();
						$uploadData[$i]['direction']='uploads/product/'.$code.'/'.$fileData['file_name'];
						$uploadData[$i]['role']='product';
						$uploadData[$i]['user_id']=$code;
						$uploadData[$i]['created'] = date("Y-m-d H:i:s");
					}else{
						$errorUploadType .= $_FILES['file']['name'].' | ';
					}
				}

				$errorUploadType = !empty($errorUploadType)?'<br/>File Type Error: '.trim($errorUploadType, ' | '):'';
				if(!empty($uploadData)){
					// Insert files data into the database
					foreach($uploadData as $row1) {
						$img_id = $this->base_model->insert('images', $row1);
						$dir=$this->base_model->get_data('images','*',array('id'=>$img_id));
						$output .= '
  			<div id="show_image_' . $img_id . '" style="border: 1px solid rebeccapurple;
  			border-radius: 10px;padding: 5px;width: 113px;height: 140px;
  			text-align: center;margin: 0 0 10px 10px;background-color: #fff">
			<span id="delete_image" id_img="' . $img_id . '" style="color: red;cursor: pointer;margin-bottom: 5px;">حذف</span>
			<img style="width: 100px;height: 100px;border: 1px solid #000;" src="' . base_url() . $dir[0]->direction .'">
			</div>	
			';
					}

					// Upload status message
/*					$statusMsg = $insert?'Files uploaded successfully!'.$errorUploadType:'Some problem occurred, please try again.';*/
				}else{
//					$statusMsg = "Sorry, there was an error uploading your file.".$errorUploadType;
				}
			}
			echo $output;
		}



	public function delete_img()
	{
		if($_POST){
			$id=$_POST['id'];
			$images=$this->base_model->get_data('images','*',array('id'=>$id));
			$this->base_model->delete('images',$id);
			unlink($images[0]->direction);
			echo 1;
		}
	}
	public function merchantid()
	{
		$data['title']='';
		$data['merchant']=$this->base_model->get_data('merchant','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/merchantid');
	}
	public function edit_merchant()
	{
		if($_POST){
			$id=$_POST['id'];
			$data['text']=$_POST['text'];
			$this->base_model->update('merchant',array('id'=>$id) ,$data);
			echo 1;
		}
	}

	public function status(){
		if ($_POST) {
			$id = $_POST['id'];
			$data['status'] = $_POST['status'];
			$this->base_model->update('shopping_cart_order', array('order_code' => $id), $data);
		}
	}






}
