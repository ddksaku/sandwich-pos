<?php
require_once ("secure_area.php");
class Deliveries extends Secure_area
{
    //var $CI;
	function __construct()
	{
		parent::__construct('phones');
        session_start();
		//$this->CI =& get_instance();
        //$this->load->library('receiving_lib');
	}

	function index()
	{
/*	   
		$person_info = $this->Employee->get_logged_in_employee_info();
		$data['companies'] = $this->Company->get_all(1000000 , 0);
        $company_id = 0;
        //$this->session->set_userdata('company_id' , $company_id);

        
        $data['delivery_info'] = $this->Phone->get_temp_delivery();
        
        $data['customer_table'] = get_phone_customer_manage_table($this , $company_id);
        $data['delivery_list'] = get_delivery_list($this , "phones");
        
        
*/
        $data['controller_name'] = strtolower(get_class());
        $_SESSION['group_id'] = -1;
        $data['delivery_table'] = get_deliveries_table($this);
        $data['delivery_group_table'] = get_delivery_group_table($this);
        $data['delivery_category'] = 1;     //is Make Delivery
        
        $this->load->view("deliveries/deliveries",$data);

	}


    function save_delivery_orders()
    {
        
        $orders_to_register = $this->input->post('ids');
        
        $group_id = $_SESSION['group_id'];

		if($this->Delivery->save_delivery_order($orders_to_register , $group_id))
		{
		    $_SESSION['group_id'] = $group_id;
			echo json_encode(array('success'=>true,'message'=>'Success'));

		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>'Error #2'));
		}
           

    }

    function get_order_table()
    {
        echo get_deliveries_table($this);
    }
    
    function get_group_table()
    {
        echo get_delivery_group_table($this);
    }
    
    
    function proc_group()
    {
        $group_id = $this->input->post('group_id');
        $proc_num = $this->input->post('proc_num');
        
        if($proc_num == 1)  //edit group
        {
            $_SESSION['group_id'] = $group_id;
            echo get_edit_deliveries_table($this , $group_id);
        }
        else if($proc_num == 2)     //delete group
        {
            $this->Delivery->delete_delivery_group($group_id);
            $_SESSION['group_id'] = -1;
            echo get_deliveries_table($this);
        }
        
    }
    
    function print_receipt($group_id)
	{
        //$result_group = $this->Delivery->get_group_by_id_row($group_id);
        //$data['result_group'] = $this->Delivery->get_group_by_id_row($group_id);

        $data['table_data'] = get_delivery_receipt_table($this , $group_id);
        //$data['result_orders'] = 
        
        $this->load->view("deliveries/receipt",$data); 

	}
    
    function change_mode($mode)
    {
        if($mode == 1)
        {
            $data['controller_name'] = strtolower(get_class());
            $_SESSION['group_id'] = -1;
            $data['delivery_table'] = get_deliveries_table($this);
            $data['delivery_group_table'] = get_delivery_group_table($this);
            $data['delivery_category'] = 1;     //is Make Delivery
            
            $this->load->view("deliveries/deliveries",$data);            
        }
        if($mode == 2)
        {
            $data['receive_payment_table'] = get_receive_payment_group_table($this);
            $data['controller_name'] = strtolower(get_class());
            $data['delivery_category'] = 2;     //is Receive Payment
            
            $this->load->view("deliveries/deliveries" , $data);
        }
    }
    
    function get_form_width()
    {
        return 350;
    }
    
    function edit_group($group_id)
    {
        $data['group_info'] = $this->Delivery->get_group_by_id_row($group_id);
        $this->load->view("deliveries/form" , $data);
    }
    
    function save($group_id)
    {
        $group_data = array(
            'amount_received'=>$this->input->post('amount_received'),
            'amount_comment'=>$this->input->post('amount_comment'),
            'completed'=>$this->input->post('group_completed')
        );
        
        $employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
        
		if($this->Delivery->save($group_data,$group_id))
		{
    		echo json_encode(array('success'=>true,'message'=>$this->lang->line('deliveries_successful_updating').' Group #'.
    		$group_id,'group_id'=>$group_id));
		}
		else//failure
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('deliveries_error_updating').' '.
			$group_id,'group_id'=>-1));
		}        
    }
    
    function refresh_group_table()
    {
        echo get_receive_payment_group_table($this);
    }
}
?>