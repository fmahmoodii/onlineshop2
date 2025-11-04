<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller
{
	public function index()
	{
		$data['title'] = 'صفحه اصلی';

		$data['attrs'] = $this->base_model->get_data('product_attributes', '*', array('default' => '1'));

		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$data['shopping_cart']=$this->base_model->get_data('shopping_cart','*');
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['category_test']=$this->base_model->get_data('category_test','*');
		$data['products']=$this->base_model->get_data('products','*');
		$data['images']=$this->base_model-> get_data('images','*',array('role'=>'product'));
		$this->load->view('home/layout/header', $data);
		$this->load->view('home/index');
		$this->load->view('home/layout/footer');
	}
	public function get_categories()
	{
		$parent_id = $this->input->post('parent_id');
		$categories = $this->base_model->get_datas('category_test', '*', 'parentId', $parent_id);

		foreach ($categories as &$category) {
			// چک کردن اینکه این کتگوری زیرشاخه داره یا نه
			$subcategories_count = $this->base_model->count_datas('category_test', 'parentId', $category->id);
			$category->has_subcategories = ($subcategories_count > 0) ? true : false;
		}

		echo json_encode($categories); // ارسال به جیسون
	}
	public function login_form()
	{
		$data['title'] = 'ثبت نام/ورود';
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$this->load->view('home/layout/header4', $data);
		$this->load->view('home/login');
		$this->load->view('home/layout/footer');
	}
	public function register()
	{
		if ($_POST) {
			$data['phone_number'] = $_POST['phone_number'];
			$data['password'] = $_POST['password'];
			$this->base_model->insert('register', $data);
			$password = $_POST['password'];
			$phone_number = $_POST['phone_number'];
			$where = "phone_number='" . $phone_number . "' And password='" . $password . "'";
			$register = $this->base_model->get_data('register', '*', $where);
			$id = $register[0]->id;
			$phone = $register[0]->phone_number;
			$this->session->set_userdata('user_id', $id);
			$this->session->set_userdata('number', $phone);
			//***********************
			$data1['phone_number'] = $_POST['phone_number'];
			$data1['user_id'] = $_SESSION['user_id'];
			$this -> base_model -> insert('profile',$data1);

			redirect('home/user_panel');
		}
	}
		public function login()
	{
		if ($_POST){
			$phone_number=$_POST['phone'];
			$password=$_POST['pass'];
			$reg =$this -> base_model -> get_data('register','*',array('phone_number'=>$phone_number,'password'=>$password));

			if(isset($reg[0])){
				if($reg[0]->isActive=='1'){
					$id=$reg[0]->id;
					$phone=$reg[0]->phone_number;
					$this->session->set_userdata('user_id',$id);
					$this->session->set_userdata('number',$phone);

					$this->session->set_userdata('id',$id);
					redirect('home/user_panel');
				}else if($reg[0]->isActive=='0'){
					$this->session->set_flashdata('err2','msg');
					redirect('home/login_form');
				}

			}else{

				$this->session->set_flashdata('err','msg');
				redirect('home/login_form');
			}

		}

	}
		public function logout(){

		$this->session->unset_userdata('user_id');
		$this->session->unset_userdata('number');
		$this->session->sess_destroy();
		redirect('home');
	}
	public function user_panel()
	{
		$data['title'] = 'پنل کاربری';
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$data['shopping_cart']=$this->base_model->get_data('shopping_cart','*');
		$data['province']=$this->base_model->get_data('province','*');
		$data['city']=$this->base_model->get_data('city','*');
		$user_id=$this->session->userdata('user_id');
		//$phone_number= $this->session->userdata('number');
		$data['profile']=$this->base_model-> get_data('profile','*',array('user_id'=>$user_id));
		//$data['profile']=$this->base_model-> get_data('profile','*',array('phone_number'=>$phone_number));
		$this->load->view('home/layout/header', $data);
		$this->load->view('home/user-panel');
		$this->load->view('home/layout/footer');
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

	public function change_pass()
	{

		$pass_before = $_POST['pass_before'];
		$data['password'] = $_POST['pass_new'];
		$register=$this->base_model->get_data('register','*',array('id' => $_SESSION['user_id']));

		if($pass_before==$register[0]->password){
			$this->base_model->update('register', array('id' =>  $_SESSION['user_id']), $data);
			echo 1;
		}else{
			echo 0;
		}

	}

	public function edit_profile($user_id)
	{
		$this->load->library('form_validation');
		$this->load->helper('form');

		$data['name'] = $_POST['name'];
		$data['family'] = $_POST['family'];
		$data['reciever_phone_number'] = $_POST['phone_number1'];
		$data['ostan'] = $_POST['ostan'];
		$data['city'] = $_POST['city'];
		$data['address'] = $_POST['address'];
		$data['postal_code'] = $_POST['postal_code'];



		/*foreach ($profile as $prof){
			$x=$prof->phone_number;
			break;
		}*/

//		if($_POST['phone_number'] != $x) {
//			$this->form_validation->set_rules('phone_number', 'profile', 'required|trim|is_unique[profile.phone_number]');
//			if($this->form_validation->run()){
//				echo 1;
//			}else{ echo 3;}
//
//		} else {
//			$this->form_validation->set_rules('phone_number', 'profile', 'required|trim');
//		}



		$this->form_validation->set_message('required', 'فیلد الزامی');
		$this->form_validation->set_message('min_length', '%s باید حداقل %d کاراکتر داشته باشد');
		$this->form_validation->set_message('max_length', '%s باید حداکثر %d کاراکتر داشته باشد');
		$this->form_validation->set_message('regex_match', 'فقط از حروف استفاده کنید');
		$this->form_validation->set_message('_phoneRegex', 'شماره وارد شده نادرست است');

		$this->form_validation->set_rules('name', 'نام', 'required|min_length[3]|max_length[25]|regex_match[/^[\pL\s\-]+$/u]');
		$this->form_validation->set_rules('family', 'نام خانوادگی', 'required|min_length[3]|max_length[25]|regex_match[/^[\pL\s\-]+$/u]');
		$this->form_validation->set_rules('phone_number1', 'شماره موبایل ضروری', 'required|callback__phoneRegex');
		$this->form_validation->set_rules('ostan', 'استان', 'required');
		$this->form_validation->set_rules('city', 'شهر', 'required');
		$this->form_validation->set_rules('address', 'آدرس', 'required');
		$this->form_validation->set_rules('postal_code', 'کد پستی', 'required');


		if($this->form_validation->run()) {
			if ($_POST) {
				date_default_timezone_set("Asia/Tehran");
				$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data['name'] = $_POST['name'];
				$data['family'] = $_POST['family'];
				//$data['phone_number'] = $_POST['phone_number'];
				$data['reciever_phone_number'] = $_POST['phone_number1'];
				$data['address'] = $_POST['address'];
				$data['ostan'] = $_POST['ostan'];
				$data['city'] = $_POST['city'];
				$data['postal_code'] = $_POST['postal_code'];


				$this->base_model->update('profile', array('user_id' => $user_id), $data);
				redirect('home/user_panel');

			}
		}else{
			$this->user_panel();
		}
	}

	public function _phoneRegex($phone_number1){
		if (preg_match('/^(\+98|0)?9\d{9}$/', $phone_number1)){
			return true;
		}else{
			return false;
		}
	}


	public function call_us()
	{
		$data['title'] = 'ارتباط با ما';
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$data['shopping_cart']=$this->base_model->get_data('shopping_cart','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$this->load->view('home/layout/header', $data);
		$this->load->view('home/call-us');
		$this->load->view('home/layout/footer');
	}
	public function insert_message()
	{
		if ($_POST){
			date_default_timezone_set("Asia/Tehran");
			$data['email'] = $_POST['email'];
			$data['name'] = $_POST['name'];
			$data['title'] = $_POST['title'];
			$data['text'] = $_POST['text'];
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$this->base_model->insert('message',$data);
			echo 1;
		}

	}
	public function rules()
	{
		$data['title'] = 'قوانین سایت';
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$data['shopping_cart']=$this->base_model->get_data('shopping_cart','*');
		$data['rules']=$this->base_model->get_data('rules','*');
		$this->load->view('home/layout/header', $data);
		$this->load->view('home/rules');
		$this->load->view('home/layout/footer');
	}
	public function product_detail($id,$code)
	{
		$data['title'] = 'جزئیات محصول';
		$data['attrs'] = $this->base_model->get_data('product_attributes', '*', array('code_p' => $code));
		if($this->session->userdata('user_id')) {
		$data['profile'] = $this->base_model->get_data('profile', '*', array('user_id' => $_SESSION['user_id']));}

		$data['model']=$this->base_model->get_data('model','*');
		$data['jens']=$this->base_model->get_data('jens','*');
		$data['brand']=$this->base_model->get_data('brand','*');
		$data['color']=$this->base_model->get_data('color','*');
		$data['size']=$this->base_model->get_data('size','*');



		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$data['shopping_cart']=$this->base_model->get_data('shopping_cart','*');
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['comments']=$this->base_model->get_data('comments','*');
		$data['comments2']=$this->base_model->get_data('comments','*');
		if($this->session->userdata('user_id')) {
			$data['bookmarks1'] = $this->base_model->get_data('bookmarks', '*', array('user_id' => $_SESSION['user_id'], 'id_p' => $id));
		}
		$data['products']=$this->base_model-> get_data('products','*',array('id'=>$id));
		$data['images']=$this->base_model-> get_data('images','*',array('role'=>'product'));
		$this->load->view('home/layout/header', $data);
		$this->load->view('home/product-detail');
		$this->load->view('home/layout/footer');
	}

	public function price()
	{
		$price= $_POST['totalprice'];
		$off_code= $_POST['off_code'];
		$this->session->set_userdata('totalprice',$price);
		$this->session->set_userdata('off_code',$off_code);
	}
	public function totalprice(){
		if ($_POST) {
			$user_id= $this->session->userdata('user_id');
			$data['final_price'] = $_POST['final_price'];
			$this->base_model->update('shopping_cart', array('user_id'=>$user_id), $data);
		}
	}
	public function shopping_cart(){

		$data['title'] = 'سبد خرید';
		$data['category1'] = $this->base_model->get_data('category1', '*');
		$data['category2'] = $this->base_model->get_data('category2', '*');
		$user_id = $this->session->userdata('user_id');
		$data['rules'] = $this->base_model->get_data('rules', '*');
		$data['call_us'] = $this->base_model->get_data('call_us', '*');
		$data['socials'] = $this->base_model->get_data('socials', '*');
		$data['links'] = $this->base_model->get_data('links', '*');
		$data['products']=$this->base_model->get_data('products','*');
		$data['shopping_cart'] = $this->base_model->get_data('shopping_cart', '*', array('user_id' => $user_id));

		$data['product_attributes'] = $this->base_model->get_data('product_attributes', '*');
		$data['model']=$this->base_model->get_data('model','*');
		$data['jens']=$this->base_model->get_data('jens','*');
		$data['brand']=$this->base_model->get_data('brand','*');
		$data['color']=$this->base_model->get_data('color','*');
		$data['size']=$this->base_model->get_data('size','*');

		$this->load->view('home/layout/header2', $data);
		$this->load->view('home/shopping_cart2');
		$this->load->view('home/layout/footer');

	}
	public function hhh(){
		$model=$this->base_model->get_data('model','*');
		$jens=$this->base_model->get_data('jens','*');
		$brand=$this->base_model->get_data('brand','*');
		$color=$this->base_model->get_data('color','*');
		$size=$this->base_model->get_data('size','*');

		foreach ($model as $mo){if($mo->id==$_POST['model']){
			$data2['model']=$mo->name;
		}if($_POST['model']==0){$data2['model']="";}}

		foreach ($jens as $je){if($je->id==$_POST['jens']){
			$data2['jens']=$je->name;
		}if($_POST['jens']==0){$data2['jens']="";}}

		foreach ($size as $si){if($si->id==$_POST['size']){
			$data2['size']=$si->name;
		}if($_POST['size']==0){$data2['size']="";}}

		foreach ($brand as $br){if($br->id==$_POST['brand']){
			$data2['brand']=$br->name;
		}if($_POST['brand']==0){$data2['brand']="";}}

		foreach ($color as $co){if($co->id==$_POST['color']){
			$data2['color']=$co->name;
		}if($_POST['color']==0){$data2['color']="";}}

//		$data2['price']=$_POST['price'];
//		$data2['isActive']='1';



		echo json_encode($data2);
	}
	public function insert_shopping_cart2(){
		$id = $_POST['id'];
		$shopping_cart=$this->base_model->get_data('shopping_cart','*',array('id'=>$id));
		$p=$shopping_cart[0]->price;
		$op=$shopping_cart[0]->off_percent;
		if($shopping_cart[0]->off_percent==null){
			$pri=$shopping_cart[0]->price;
		}else{
			$pri=$p-($op/100)*$p;
		}
		$qty = $_POST['qty'];
		$data['qty'] = $qty;
		$data['price'] = $p;
		$data['total_price1'] = $qty*$p;
		$data['off_percent'] = $op;
		$data['off_price'] = ($p-$pri)*$qty;
		$data['total_price'] = $qty*$pri;
		date_default_timezone_set("Asia/Tehran");
		$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
		$this->base_model->update('shopping_cart',array('id'=>$id), $data);

		$arr=array($qty,$p,$qty*$p,$op,($p-$pri)*$qty,$qty*$pri);
		echo json_encode($arr);

	}
	public function insert_shopping_cart22(){
		$id = $_POST['id_p'];
		$products=$this->base_model->get_data('products','*',array('id'=>$id));
		$shopping_cart=$this->base_model->get_data('shopping_cart','*',array('id_p'=>$id));
		$p=$products[0]->price;
		$op=$products[0]->off_percent;
		if($products[0]->off_percent==null){
			$pri=$products[0]->price;
		}else{
			$pri=$p-($op/100)*$p;
		}

		$qty1 = $shopping_cart[0]->qty;
		$qty2 = $_POST['qty'];
		$qty = $qty1 + $qty2;
		$data['user_id'] = $this->session->userdata('user_id');
		$data['id_p'] = $_POST['id_p'];
		$data['name'] = $_POST['name'];
		$data['qty'] = $qty;
		$data['price'] = $p;
		$data['off_percent'] = $op;
		$data['off_price'] = ($p-$pri)*$qty;
		$data['total_price1'] = $qty*$p;
		$data['total_price'] = $qty*$pri;

		if($shopping_cart[0]->id_p!=$id){
			$this->base_model->insert('shopping_cart', $data);
		}
		else{
			$this->base_model->update('shopping_cart',array('id_p'=>$id), $data);

		}


	}
	public function insert_shopping_cart(){
		$id_p = $_POST['id_p'];
		$id_attr = $_POST['id_attr'];
		$products=$this->base_model->get_data('products','*',array('id'=>$id_p));
		$product_attrs=$this->base_model->get_data('product_attributes','*',array('id'=>$id_attr));
		$shopping_cart=$this->base_model->get_data('shopping_cart','*',array('id_attr'=>$id_attr));
		$n=$products[0]->name;

		$c=$products[0]->code;
		$p=$product_attrs[0]->price;
		$op=$product_attrs[0]->off_percent;
		if($product_attrs[0]->off_percent==null){
			$pri=$product_attrs[0]->price;
		}else{
			$pri=$p-($op/100)*$p;
		}
if(isset($shopping_cart[0])){
	$qty1 = $shopping_cart[0]->qty;
}else{
	$qty1 = 0;
}

		$qty2 = $_POST['qty'];
		$qty = $qty1 + $qty2;
		$data['user_id'] = $this->session->userdata('user_id');
		$data['code_p'] = $c;
		$data['id_attr'] = $id_attr;
		$data['id_p'] = $id_p;
		$data['name'] = $n;
		$data['qty'] = $qty;
		$data['price'] = $p;
		$data['off_percent'] = $op;
		$data['off_price'] = ($p-$pri)*$qty;
		$data['total_price1'] = $qty*$p;
		$data['total_price'] = $qty*$pri;

		if(!isset($shopping_cart[0])){
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$this->base_model->insert('shopping_cart', $data);
		}
		else{
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$this->base_model->update('shopping_cart',array('id_attr'=>$id_attr), $data);

		}


	}
	public function add_payment(){
		$data['payment'] = $_POST['payment'];
		$user_id= $this->session->userdata('user_id');
		$this->base_model->update('shopping_cart', array('user_id'=>$user_id), $data);

	}
	public function check_code()
	{
	//	date_default_timezone_set("Asia/Tehran");
		$date = date('Y-m-d');
		$code = $_POST['code'];
		$final = $_POST['final_price'];
		$where = "code='" . $code . "' And start_date <='" . $date . "' And end_date >='" . $date . "'";
		$off = $this->base_model->get_data('off_code', '*', $where);
		$shopping_cart = $this->base_model->get_data('shopping_cart', '*',array('user_id'=>$this->session->userdata('user_id')));
		$user_id= $this->session->userdata('user_id');

		if (isset($off[0])) {

			$mablagh = $off[0]->mablagh;
			$data1['off_code_mablagh']=$mablagh;
			$data1['off_code']=$code;
			$data1['payment']=$final-$mablagh;
			$this->base_model->update('shopping_cart', array('user_id'=>$user_id), $data1);

			if (isset($shopping_cart[0]->user_id)) {if($final>$mablagh){
					$x=$final-$mablagh;
					echo number_format($x);
				} else {
					echo 1;
				}

			}

		} else {
			$data2['payment']=$final;
			$this->base_model->update('shopping_cart', array('user_id'=>$user_id), $data2);
			echo 2;

		}
	}
	public function delete_code(){
		$user_id= $this->session->userdata('user_id');
		$data['off_code_mablagh']='';
		$data['off_code']='';
		$data['payment']='';
		$this->base_model->update('shopping_cart', array('user_id'=>$user_id), $data);
	}
	public function delete_cart()
	{
		if($_POST){
			$id=$_POST['id_cart'];
			$this->base_model->delete('shopping_cart',$id);
			echo 1;
		}
	}
	public function final_shopping_cart()
	{
		$data['title']='تایید نهایی خرید';
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$user_id= $this->session->userdata('user_id');
		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['shopping_cart1']=$this->base_model->get_data('shopping_cart','*',array('user_id'=>$user_id));
		$data['profile']=$this->base_model->get_data('profile','*',array('user_id'=>$user_id));
		$data['info_fact']=$this->base_model->get_data('info_fact','*',array('user_id'=>$user_id));
		$data['off_code']=$this->base_model->get_data('off_code','*');
		$this->load->view('home/layout/header2',$data);
		$this->load->view('home/final-shopping-cart');
		$this->load->view('home/layout/footer');

	}
	public function info_factor()
	{
		if ($_POST) {
			$data['user_id']= $this->session->userdata('user_id');
			$data['name'] = $_POST['name'];
			$data['address'] = $_POST['address'];
			$data['postal_code'] = $_POST['postal_code'];
			$data['phone_number'] = $_POST['phone_number'];
			$random_code=rand(000000000,999999999);
			$data['random_code'] = $random_code;
			$this->base_model->insert('info_fact', $data);
			echo $random_code;
		}
	}
	public function edit_info(){
		if ($_POST) {
			$user_id= $this->session->userdata('user_id');
			$data['name'] = $_POST['name'];
			$data['address'] = $_POST['address'];
			$data['postal_code'] = $_POST['postal_code'];
			$data['phone_number'] = $_POST['phone_number'];
			$this->base_model->update('info_fact', array('user_id' => $user_id), $data);
		}
	}
	public function add_bookmark()
	{
		if ($_POST) {
			$data['user_id']= $this->session->userdata('user_id');
			$data['id_p']= $_POST['id_p'];
			$this->base_model->insert('bookmarks', $data);
		}
	}
	public function delete_bookmark()
	{
		if ($_POST) {
			$data['user_id']= $this->session->userdata('user_id');
			$id= $_POST['id_p'];
			$this->base_model->delete_row('bookmarks','id_p',$id);
		}
	}
	public function bookmarks()
	{
		$data['title'] = 'علاقه مندی ها';

		$data['attrs'] = $this->base_model->get_data('product_attributes', '*', array('default' => '1'));

		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$user_id= $this->session->userdata('user_id');

		$data['bookmarks']=$this->base_model-> get_data('bookmarks','*',array('user_id'=>$user_id));
		$data['products']=$this->base_model-> get_data('products','*');
		$data['shopping_cart']=$this->base_model->get_data('shopping_cart','*');
		$this->load->view('home/layout/header', $data);
		$this->load->view('home/bookmarks');
		$this->load->view('home/layout/footer');
	}
	public function category($id)
	{
		$data['title'] = '';

		$data['attrs'] = $this->base_model->get_data('product_attributes', '*', array('default' => '1'));

		// گرفتن دسته فعلی و زیرشاخه‌ها
		$category_ids = $this->base_model->get_all_category_ids($id);

		// گرفتن محصولات
		$this->db->where_in('id_cat2', $category_ids);
		$data['products'] = $this->db->get('products')->result();

		$data['category'] = $this->base_model->get_data('category_test', '*', array('id' => $id), NULL, array('parentId' => $id));

		$data['rules'] = $this->base_model->get_data('rules', '*');
		$data['call_us'] = $this->base_model->get_data('call_us', '*');
		$data['socials'] = $this->base_model->get_data('socials', '*');
		$data['links'] = $this->base_model->get_data('links', '*');
		$data['images'] = $this->base_model->get_data('images', '*');
		$data['shopping_cart'] = $this->base_model->get_data('shopping_cart', '*');
		$data['id'] = $id;
		$data['subcategories'] = $this->base_model->get_data('category_test', '*', array('parentId' => $id));
		$this->load->view('home/layout/header', $data);
		$this->load->view('home/category');
		$this->load->view('home/layout/footer');
	}
	private function getAllSubcategoryIds($parentId) {
		$ids = [$parentId];
		$subcategories = $this->base_model->get_data('category_test', '*', ['parentId' => $parentId]);
		foreach ($subcategories as $subcat) {
			$ids = array_merge($ids, $this->getAllSubcategoryIds($subcat->id));
		}
		return $ids;
	}
	public function get_products_by_category()
	{
		$cat_id = $this->input->post('cat_id');

		$this->load->model('base_model');

		// گرفتن اطلاعات دسته فعلی
		$current_category_list = $this->base_model->get_data('category_test', '*', ['id' => $cat_id]);
		$current_category = !empty($current_category_list) ? $current_category_list[0] : null;

//		$category_ids = [$cat_id];
//
//		if ($current_category && is_object($current_category) && $current_category->parentId == 0) {
//			$sub_ids = $this->base_model->get_all_subcategory_ids($cat_id);
//			$category_ids = array_merge([$cat_id], $sub_ids);
//		}

		// این رو بذار که تمام زیرشاخه‌ها رو تا آخرین سطح بیاره:
		$category_ids = $this->getAllSubcategoryIds($cat_id);

		// گرفتن محصولات
		$this->db->where_in('id_cat2', $category_ids);
		$products = $this->db->get('products')->result();

		// گرفتن اطلاعات دیگر مورد نیاز
		$attrs = $this->base_model->get_data('product_attributes', '*', ['default' => 1]);
		$images = $this->base_model->get_data('images', '*');

		// ساخت HTML برای نمایش
		$html = '';
		foreach ($products as $p) {
			$img_src = '';
			foreach ($images as $img) {
				if ($img->user_id == $p->code) {
					$img_src = base_url() . $img->direction;
					break;
				}
			}

			$price = '';
			foreach ($attrs as $t) {
				if ($t->code_p == $p->code) {
					$price = number_format($t->price);
					break;
				}
			}

			$html .= '
			<div class="col-md-3" style="padding-bottom: 35px">
				<a href="' . base_url('home/product_detail/') . $p->id . '">
					<article style="height:auto;margin: 0 auto;background: #fff;
						direction: rtl;border-radius: 7px;padding: 20px;
						box-shadow: 0px 0px 10px 0px #cec9c9;text-align: center;">
						<img style="width:100%; height:220px" src="' . $img_src . '">
						<h4 style="height: auto; clear: both;
							margin: 15px auto;display: block;
							overflow: hidden;text-overflow: ellipsis;text-align: center">
							<a href="' . base_url('home/product_detail/') . $p->id . '">' . $p->name . '</a>
						</h4>
						<h5 style="text-align: center;line-height: 22px;">
							<span style="font-weight: bold; display: inline">قیمت:</span>
							<span>' . $price . '</span>
						</h5>
					</article>
				</a>
			</div>';
		}

		if (empty($html)) {
			$html = '<p>محصولی یافت نشد.</p>';
		}

		echo $html;
	}




	public function category1($id)
	{
		$data['title'] = '';

		$data['attrs'] = $this->base_model->get_data('product_attributes', '*', array('default' => '1'));

		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$data['images']=$this->base_model->get_data('images','*');
		$data['products']=$this->base_model-> get_data('products','*');
		$data['shopping_cart']=$this->base_model->get_data('shopping_cart','*');
		$this->load->view('home/layout/header', $data);
		$this->load->view('home/category1');
		$this->load->view('home/layout/footer');
	}
	public function category2($id)
	{
		$data['title'] = '';

		$data['attrs'] = $this->base_model->get_data('product_attributes', '*', array('default' => '1'));


		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$data['images']=$this->base_model->get_data('images','*');
		$data['products']=$this->base_model-> get_data('products','*',array('id_cat2'=>$id));
		$products=$this->base_model-> get_data('products','*',array('id_cat2'=>$id));
		$data['shopping_cart']=$this->base_model->get_data('shopping_cart','*');
		$this->load->view('home/layout/header', $data);
		$this->load->view('home/category2');
		$this->load->view('home/layout/footer');
	}
	//<<---------------date_shamsi_ghamari---------------->>
	function date_j($miladi_date){
		//gregorian_to_jalali without time
		$exploadeddate = explode(' ',$miladi_date);
		$gmtdate = explode('-',$exploadeddate[0]);
		$persiandate=$this->jalali_date->gregorian_to_jalali($gmtdate[0],$gmtdate[1],$gmtdate[2],'/');
		return $persiandate;
	}
	//<<---------------end date_shamsi_ghamari---------------->>
	public function insert_comment()
	{
		date_default_timezone_set("Asia/Tehran");
		$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
		$data['code']=$_POST['code'];
		$data['user_id']=$_POST['user_id'];
		$data['parent_id']=$_POST['parent_id'];
		$data['name']=$_POST['name'];
		$data['text']=$_POST['text'];
		$data['role']='0';
		$data['cond']='در انتظار بررسی';
		$this->base_model->insert('comments', $data);
		echo 1;
	}
	public function get_order_code()
	{
		date_default_timezone_set("Asia/Tehran");
		$user_id=$this->session->userdata('user_id');
		$order_code=rand(000000000,999999999);
		$this->session->set_userdata('order_code',$order_code);
		$shopping_cart=$this->base_model->get_data('shopping_cart','*',array('user_id'=>$user_id));

		$random_code=$_POST['r'];
		$data2['ord_code']= $this->session->userdata('order_code');
		$this->base_model->update('info_fact',array('random_code'=>$random_code),$data2);

		if (isset($order_code)){

			foreach ($shopping_cart as $cart) {
				$data['user_id']= $user_id;
				$data['id_p']= $cart->id_p;
				$id_p= $cart->id_p;
				$data['code_p']= $cart->code_p;
				$code_p= $cart->code_p;
				$data['id_attr']= $cart->id_attr;
				$id_attr= $cart->id_attr;
				$data['name']= $cart->name;
				$data['price']= $cart->price;
				$data['qty']= $cart->qty;
				$qty= $cart->qty;
				$data['off_percent']= $cart->off_percent;
				$data['off_price']= $cart->off_price;
				$data['total_price1']= $cart->total_price1;
				$data['total_price']= $cart->total_price;
				$data['final_price']= $cart->final_price;
				$data['off_code']= $cart->off_code;
				$data['payment']= $cart->payment;
				$data['off_code_mablagh']= $cart->off_code_mablagh;
				$data['order_code']= $this->session->userdata('order_code');
				$data['date']= $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data['status']= 'در انتظار ارسال';
				$product_attr = $this->base_model->get_data('product_attributes','*',array('code_p'=>$code_p,'id'=>$id_attr));
				if(isset($product_attr[0])){
					$supply1 = $product_attr[0]->supply;
				}
				$supply2 = $supply1 - $qty;
				$data3['supply']=$supply2;
				$this->base_model->insert('shopping_cart_order', $data);

			}

		}
		$this->base_model->delete_cart('shopping_cart',$user_id);
		$this->base_model->update('product_attributes', array('code_p'=>$code_p,'id'=>$id_attr), $data3);
	}
	public function order_code(){
		$data['title']='کد رهگیری';
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$user_id=$this->session->userdata('user_id');
		$data['order_code']=$this->base_model->get_data('shopping_cart_order','order_code',array('user_id'=>$user_id));
		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$this->load->view('home/layout/header2',$data);
		$this->load->view('home/order_code');
		$this->load->view('home/layout/footer');
	}
	public function get_off(){
		date_default_timezone_set("Asia/Tehran");
		$date = $this->date_j(date('Y-m-d'));
		$code= $_POST['code'];
		$off_code=$this->base_model->get_data('off_code','*');
		foreach ($off_code as $row){
			if ($row->code == $code && $row->start_date < $date && $row->end_date > $date){
				return 1;
			}
		}
	}
	public function factors()
	{
		$data['title']='اطلاعات خرید';
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$user_id=$this->session->userdata('user_id');
		$data['info_fact']=$this->base_model->get_data('info_fact','*',array('user_id'=>$user_id));
		$data['profile']=$this->base_model->get_data('profile','*',array('user_id'=>$user_id));
		$data['shopping_cart']=$this->base_model->get_data('shopping_cart','*',array('user_id'=>$user_id));
		$data['shopping_cart_order']=$this->base_model->get_data('shopping_cart_order','*',array('user_id'=>$user_id),null,null,null,null,null,null,'order_code');
		$this->load->view('home/layout/header',$data);
		$this->load->view('home/factors');
		$this->load->view('home/layout/footer');

	}
	public function f_detail($id)
	{
		$data['title']='جزئیات خرید';
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$user_id=$this->session->userdata('user_id');
		$data['shopping_cart']=$this->base_model->get_data('shopping_cart','*',array('user_id'=>$user_id));
		$data['info_fact']=$this->base_model->get_data('info_fact','*',array('user_id'=>$user_id , 'ord_code'=>$id));
		$data['shopping_cart_order']=$this->base_model->get_data('shopping_cart_order','*',array('order_code'=>$id));
		$data['products'] = $this->base_model->get_data('products', '*');
		$this->load->view('home/layout/header',$data);
		$this->load->view('home/f_detail');
		$this->load->view('home/layout/footer');


	}
	public function search()
	{
		$data['title']='جستجو';
		$data['category1']=$this->base_model->get_data('category1','*');
		$data['category2']=$this->base_model->get_data('category2','*');
		$this->load->library("cart");
		$data['products']=$this->base_model-> get_data('products','*');
		$data['images']=$this->base_model-> get_data('images','*');
		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$this->load->view('home/layout/header3',$data);
		$this->load->view('home/search_page');
		$this->load->view('home/layout/footer');
	}

	function search_name()
	{
		$output = '';
		$query = '';
		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}

		$data = $this->base_model->search_product($query);
		$where="role='product'";
		$data2 = $this->base_model->get_data('images','*',$where);

		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
				{
					$output .= ' <div class="col-md-3" style="margin-bottom: 20px">
			<article style="height:auto;margin: 0 auto;background: #fff;
                direction: rtl;border-radius: 7px;padding: 20px; box-shadow: 0px 0px 10px 0px #cec9c9;
                 ">';
					foreach ($data2 as $row2) {
						if ($row2->user_id == $row->code) {
							$output .= ' <img style=" width:100%; height:180px;" 
							src="' . base_url()  .''. $row2->direction . '" >';
							break;
						}
					}
					$output .= '
					<div style="display: block"><h4 style="height: auto; clear: both;
                    margin: 15px auto;display: block; ;
                    overflow: hidden;text-overflow: ellipsis;">
					<a id="name_'.$row->id.'" href="' . base_url()  . 'home/product_detail/'.$row->id .'">
						'.$row->name.'
					</a>
					
					</h4>
					<h5 style="line-height: 22px;">
					<span style="font-weight: bold;">قیمت:</span>
					<span>'.number_format($row->price).'</span>
					</h5>
				 <!--	<input type="number" class="form-control" style="width: 80px;display:inline ">
					<i class="fa fa-plus-square" style="color: green;font-size: 35px;margin-top: 10px"></i>-->
					<b style="font-size: 16px; margin-left: 25px;line-height: 36px">تعداد:</b><br>
					
					<input id="qty_'.$row->id .'"
					   type="number" step="1" min="1" max="100" value="1" size="4"
					   pattern="[0-9]*" inputmode="numeric" style="width: 90px;height: 37px;padding: 9px;font-size: 16px;
                           border-radius: 5px;border: 1px solid #809c80;outline: unset;">
				
				
						<button id="shopping_cart" id_p="'.$row->id .'" class="btn btn-success" style="width: 100%;padding: 15px;outline: unset;
                        border-radius: 8px;border: 1px solid #ccc;box-shadow: 3px 3px 3px #dbdbdb;margin-top: 28px;">
							<span style="font-size: 18px;"> افزودن به سبد خرید</span>
						</button>
					
			
<!--
					<span>در سبد خرید شما</span>
-->
					</div>
					
				</article>
				</div>
				';

				}}
		else
		{
			$output .= '
            <div style="text-align: center;height: 50px;padding-top: 15px;border-radius: 15px">
            <b>موردی یافت نشد دوباره امتحان کنید</b>
		    </div>
		    ';
		}
		echo $output;
	}



	function code_meli_validation(){
		$code= $_POST['code'];
		$get = json_decode(file_get_contents("https://api.codebazan.ir/codemelli/?code=".$code), true);
		echo $get['result']['1'];
	}




	function prof_validation(){
		$id=$_POST['id'];
		$data['name'] = $_POST['name'];
		$data['family'] = $_POST['family'];
		$data['phone_number1'] = $_POST['phone_number1'];
		$data['ostan'] = $_POST['ostan'];
		$data['city'] = $_POST['city'];
		$data['address'] = $_POST['address'];
		$data['postal_code'] = $_POST['postal_code'];

		$profile=$this->base_model->get_data('profile','*',array('id'=>$id));
		if(isset($profile[0])){
			$x=$profile[0]->phone_number;
		}

		if($_POST['phone_number'] != $x) {
			$this->form_validation->set_rules('phone_number', 'profile', 'required|trim|is_unique[profile.phone_number]');
			if($this->form_validation->run()){
				echo 1;
			}else{ echo 3;}

		} else {
			$this->form_validation->set_rules('phone_number', 'profile', 'required|trim');
		}

		$this->form_validation->set_rules('name', 'name', 'required|trim|min_length[3]');
		$this->form_validation->set_rules('family', 'family', 'required|trim|min_length[3]');
		$this->form_validation->set_rules('ostan', 'ostan', 'required|trim|min_length[3]');
		$this->form_validation->set_rules('city', 'city', 'required|trim|min_length[3]');
		$this->form_validation->set_rules('address', 'address', 'required|trim|min_length[3]');


		if($this->form_validation->run())
		{
			echo 1;
		}else{
			if($_POST["name"]=='' || strlen($_POST['name'])<3){
				echo 2;
			}
			if(!($_POST["phone_number"]>0) || $_POST["phone_number"]==''){
				echo 3;
			}
		}


	}

}


