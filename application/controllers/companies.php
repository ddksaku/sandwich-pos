<?php
require_once ("secure_area.php");
require_once (APPPATH."libraries/ofc-library/open-flash-chart.php");
class Companies extends Secure_area
{
	function __construct()
	{
		parent::__construct('companies');

	}

	//Initial report listing screen
	function index($mode = 'default' , $search_page = '' , $sort_key = 1 , $per_page = 30)
	{

		if($mode == 'default')
		{
			$uri_segment = 6;
			$sort_key = $this->uri->segment(4);
			$per_page = $this->uri->segment(5);
			if($per_page == 0) $per_page = 30;
			if($sort_key > 8 || $sort_key < 1) $sort_key = 1;

			$data['total_rows'] = $this->Company->count_all();
			$data['total_page'] = floor($data['total_rows'] / $per_page) + 1;
			$data['per_page'] = $per_page;
			$data['uri_segment'] = 6;
		}
		else if($mode == 'search')
		{
			$uri_segment = 7;
			$sort_key = $this->uri->segment(5);
			$per_page = $this->uri->segment(6);
			if($per_page == 0) $per_page = 30;
			if($sort_key > 8 || $sort_key < 1) $sort_key = 1;
			if($search_page == "12345678901234567890") $search = "";
			else $search = $search_page;

			$data['total_rows'] = $this->Company->total_search_num_rows($search);
			$data['total_page'] = floor($data['total_rows'] / $per_page) + 1;
			$data['per_page'] = $per_page;
			$data['uri_segment'] = $uri_segment;
		}

		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();

		if($mode == 'default')
			$data['manage_table'] = get_companies_manage_table(
					$this->Company->get_all(
							$data['per_page'] ,
							$this->uri->segment( $data['uri_segment'] ,
							$sort_key)) ,
					$this ,
					$sort_key);
		else if($mode == 'search')
		{
			if($search_page == "12345678901234567890")
				$search = "";
			else
				$search = $search_page;

			$data['manage_table'] = get_companies_manage_table(
					$this->Company->search(
							$search ,
							$data['per_page'] ,
							$this->uri->segment($data['uri_segment']) ,
							$sort_key) ,
					$this ,
					$sort_key);
			$data['search'] = $search;
		}
		$data['sort_key'] = $sort_key;
		$data['curd_page'] = $this->uri->segment($uri_segment) / $per_page + 1;
		$data['search_mode'] = $mode;
        $this->load->view("companies/manage" , $data);
	}

    function view($company_id)
    {
		$data['companies_info']=$this->Company->get_info($company_id);

		$this->load->view("companies/form",$data);
    }

	function suggest_company()
	{
		$suggestions = $this->Company->get_companies_suggestions($this->input->post('q'));
		echo implode("\n",$suggestions);
	}

    function get_row()
	{
		$company_id = $this->input->post('row_id');
		$data_row = get_company_data_row($this->Company->get_info($company_id),$this);
		echo $data_row;
	}


    function save($company_id=-1)
    {
    	$company_id = $this->input->post('company_id');
    	if($company_id == 0 || $company_id == '') $company_id = -1;
		$company_data = array(
    		'name'=>$this->input->post('company_name'),
    		'contact_number'=>$this->input->post('contact_number'),
    		'contact_address'=>$this->input->post('contact_address'),
    		'post_code'=>$this->input->post('post_code')
		);

		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$cur_company_info = $this->Company->get_info($company_id);


		if($this->Company->save($company_data,$company_id))
		{
			//New item
			if($company_id==-1)
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('companies_successful_adding').' '.
				$company_data['name'],'company_id'=>$company_data['company_id']));
				$company_id = $company_data['company_id'];
			}
			else //previous item
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('companies_successful_updating').' '.
				$company_data['name'],'company_id'=>$company_id));
			}
		}
		else//failure
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('companies_error_adding_updating').' '.
			$company_data['name'],'company_id'=>-1));
		}

    }

    function suggest()
	{
		$suggestions = $this->Company->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}

	function search()
	{
		$search=$this->input->post('search');
		$data_rows=get_companies_manage_table_data_rows($this->Company->search($search),$this);
		echo $data_rows;
	}


    function delete()
	{
		$companies_to_delete=$this->input->post('ids');

		if($this->Company->delete_list($companies_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('items_successful_deleted').' '.
			count($companies_to_delete).' '.$this->lang->line('items_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('items_cannot_be_deleted')));
		}
	}


	function get_form_width()
	{
		return 360;
	}

	function get_company_info()
	{
		$company_id = $this->input->post('company_id');
		$company_info = array();
		$get_info = $this->Company->get_info($company_id);
		$company_info[] = $get_info->name;
		$company_info[] = $get_info->contact_number;
		$company_info[] = $get_info->contact_address;
		$company_info[] = $get_info->post_code;
		echo json_encode($company_info);
	}
}
?>