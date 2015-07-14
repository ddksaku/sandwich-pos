<?php
class Login extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		if($this->Employee->is_logged_in())
		{
			redirect('home');
		}
		else
		{

			$this->form_validation->set_rules('username', 'lang:login_undername', 'callback_login_check');
    	    $this->form_validation->set_error_delimiters('<div class="error">', '</div>');


			if($this->form_validation->run() == FALSE)
			{
				$data['employees_info'] = $this->Employee->get_all(10000 , 0);
				$this->load->view('login' , $data);
			}
			else
			{
				redirect('home');
			}

//			$data['employees_info'] = $this->Employee->get_all(10000 , 0);
//			$this->load->view('login');
		}
	}

	function login_check($username)
	{
		$password = $this->input->post("password");

		if($username != "admin") $password = "1";


		if(!$this->Employee->login($username,$password))
		{
			$this->form_validation->set_message('login_check', $this->lang->line('login_invalid_username_and_password'));
			return false;
		}
		return true;
	}
}
?>