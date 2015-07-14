<?php
require_once ("secure_area.php");
require_once ("interfaces/idata_controller.php");
class Categories extends Secure_area implements iData_controller
{
	function __construct()
	{
		parent::__construct('categories');
	}

	function index()
	{
		$config['base_url'] = site_url('/categories/index');
		$config['total_rows'] = $this->Category->count_all();
		$config['per_page'] = '20';
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);
		
		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();
		$data['manage_table']=get_categories_manage_table( $this->Category->get_categories( 10000, 0), $this , true);
        
		$this->load->view('categories/manage',$data);
	}


	function refresh()
	{
		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();
		$data['manage_table']=get_categories_manage_table($this->Category->get_all_filtered($low_inventory,$is_serialized,$no_description),$this);
		$this->load->view('categories/manage',$data);
	}
    
    
    
	function search()
	{
		$search=$this->input->post('search');
		//$data_rows=get_categories_manage_table_data_rows($this->Category->search($search),$this);
        $data_rows = get_categories_manage_table( $this->Category->search($search), $this ,false);
		echo $data_rows;
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
		$suggestions = $this->Item->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}
    
	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest_category()
	{
		$suggestions = $this->Category->get_category_suggestions($this->input->post('q'));
		echo implode("\n",$suggestions);
	}

    
   	function get_row()
	{
		$category_id = $this->input->post('row_id');
		$data_row=get_category_data_row($this->Category->get_info($category_id),$this);
		echo $data_row;
	}

	function view($category_id=-1)
	{
		$data['category_info']=$this->Category->get_info($category_id);
        $data['parent_categories']=$this->Category->get_categories(10000 , 0);
        
		$this->load->view("categories/form",$data);
	}
    
    
    function save($category_id=-1)
	{
		$category_data = array(
		'name'=>$this->input->post('name'),
		'parent_id'=>$this->input->post('parents_categories'),
        'deleted'=>$this->input->post('deleted')
		);
		
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$cur_category_info = $this->Category->get_info($category_id);


		if($this->Category->save($category_data,$category_id))
		{
		    $data_table = get_categories_manage_table( $this->Category->get_categories( 10000, 0), $this , true);
			//New item
			if($category_id==-1)
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('categories_successful_adding').' '.
				$category_data['name'],'category_id'=>$category_data['parents_categories'],'responseText'=>$data_table));
				
			}
			else //previous item
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('categories_successful_updating').' '.
				$category_data['name'],'category_id'=>$category_data['parents_categories'],'responseText'=>$data_table));
			}
			
		}
		else//failure
		{
		  $data_table = get_categories_manage_table( $this->Category->get_categories( 10000, 0), $this , true);
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('categories_error_adding_updating').' '.
			$category_data['name'],'parents_categories'=>-1,'responseText'=>$data_table));
		}

	} 
    
    
	function delete()
	{
		$categories_to_delete=$this->input->post('ids');
        


		if($this->Category->delete_list($categories_to_delete))
		{
		  	$data_table = get_categories_manage_table( $this->Category->get_categories( 10000, 0), $this , true);
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('items_successful_deleted').' '.
			count($categories_to_delete).' '.$this->lang->line('items_one_or_multiple'),'responseText'=>$data_table));
		}
		else
		{
		    $data_table = get_categories_manage_table( $this->Category->get_categories( 10000, 0), $this , true);
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('items_cannot_be_deleted'),'responseText'=>$data_table));
		}
       
	}
    
	function get_form_width()
	{
		return 360;
	} 
    
    
    
           
}
?>