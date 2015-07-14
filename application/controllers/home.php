<?php
require_once ("secure_area.php");

class Home extends Secure_area
{
	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
//		$user_info = $this->Employee->get_logged_in_employee_info();
//		if($user_info->username == "admin")
			$this->load->view("home");
//		else
//			redirect("sales");
	}

	function logout()
	{
		$this->Employee->logout();
	}
}
?>