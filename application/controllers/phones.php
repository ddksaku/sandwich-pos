<?php
require_once ("secure_area.php");
class Phones extends Secure_area
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
		$person_info = $this->Employee->get_logged_in_employee_info();
		$data['companies'] = $this->Company->get_all(1000000 , 0);
        $company_id = 0;
        //$this->session->set_userdata('company_id' , $company_id);

        
        $data['delivery_info'] = $this->Phone->get_temp_delivery();
        
        $data['customer_table'] = get_phone_customer_manage_table($this , $company_id);
        $data['delivery_list'] = get_delivery_list($this , "phones");
        $data['controller_name'] = strtolower(get_class());
        $_SESSION['phone_category'] = 1;
        $data['phone_category'] = 1;
        $this->load->view("phone/phone",$data);

	}

	function _reload($data=array())
	{
	}

	function search()
	{
		$search = $this->input->post('search');
        //$company_id = $this->session->userdata('company_id');
        $company_id = $_SESSION['company_id'];
		$data_rows = get_phone_customer_manage_table_search($this->Phone->search($search , $company_id),$this);
		echo $data_rows;
	}

	function suggest()
	{
		//$company_id = $this->session->userdata('company_id');
        $company_id = $_SESSION['company_id'];
        //echo $company_id;
        $suggestions = $this->Phone->get_search_suggestions($this->input->post('q') , $company_id , 100000);
		echo implode("\n",$suggestions);
	}
    
    function change_company()
    {
        $company_id = $this->input->post('company_id');
        $phone_category = $this->input->post('phone_category');
        //$this->session->set_userdata('company_id' , $company_id);
        $_SESSION['company_id'] = $company_id;
        if($company_id != 0)
        {
            $company_info = $this->Company->get_info($company_id);
            $company_str1 = get_phone_customer_manage_table($this , $company_id);
            if($phone_category == 1)
            {
                $company_str2 = $company_info->contact_number."^^^^^".$company_info->contact_address."^^^^^".$company_info->post_code;
            }
            else if($phone_category == 2)
            {
                $company_str2 = $company_info->contact_number;
            }    
            $company_str1 = $company_str1."**********".$company_str2;
            echo $company_str1;
        }
        else
            echo get_phone_customer_manage_table($this , $company_id);
    }
    

    function save_collect_customer()
    {
        $customers_to_register = $this->input->post('ids');
        //echo $customers_to_register[5];
        
		if($this->Phone->save_collect_customers($customers_to_register))
		{
			echo json_encode(array('success'=>true,'message'=>'Success'));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>'Error #1'));
		}
           
    }

    
    function save_delivery_customer()
    {
        $customers_to_register = $this->input->post('ids');
        //echo $customers_to_register[5];
        
		if($this->Phone->save_delivery_customers($customers_to_register))
		{
			echo json_encode(array('success'=>true,'message'=>'Success'));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>'Error #2'));
		}
           
    }

    function get_collect_table()
    {
        echo get_collect_list($this , "phones");
    }
    
        
    function get_delivery_table()
    {
        echo get_delivery_list($this , "phones");
    }


    function edit_collect_sale($customer_id)
    {
        $cate = $this->Sale->get_categories();

        $nInc = 0;
        foreach($cate->result() as $cate1)
        {
            if($nInc == 0)
                $first_num = $cate1->category_id;
            $nInc ++;    
        }
        $bInsert = $this->Sale->delete_all_temp();
        
        $collect_orders_info = $this->Phone->get_temp_collect_orders($customer_id);
        
        $sale_id = $collect_orders_info->sale_id;
        
        if($sale_id == 0)
            $sale_id = -1;

        $sale_results = $this->Sale->get_sale_items($sale_id);
        foreach($sale_results->result() as $sale_res)
        {
            $bInsert = $this->Sale->insert_temp_data(1 , $sale_res->item_id , $sale_res->quantity_purchased);    
        }
        
        
        $data['categories'] = $this->Sale->get_categories();
        $data['sub_categories'] = $this->Sale->get_sub_categories($first_num);
        $data['items'] = get_sale_item_rows($this->Sale->get_sub_categories($first_num) , $this);
        $data['sales_table'] = get_delivery_sales_rows($this , "sales");
        $data['suspended_sales_table'] = get_delivery_suspended_sales_rows($this , "sales");
        $data['controller_name'] = strtolower("sales");
        $data['delivery_and_collect'] = 2;  //is Collect!
        
                


        $selected_table_items = get_refresh_table($this);
        list($selected_items_table , $total_amount) = explode("," , $selected_table_items);
        $data['selected_items_table'] = $selected_items_table;
        $data['total_amount'] = $total_amount;

        $amt_tendered = $this->Sale->get_payment_amount($sale_id);
        $data['amt_tendered'] = $amt_tendered;
        $data['change_amount'] = $amt_tendered - $total_amount;        


        $data['sale_id'] = $sale_id;
        $data['customer_id'] = $customer_id;

        
        if($customer_id!=-1)
		{
			$info=$this->Customer->get_info($customer_id);
			$data['customer']=$info->first_name.' '.$info->last_name;
            
			$data['customer_email']=$info->email;
		}
        
        
        
        $data['sale_status'] = 1;  //sale_status is new!
        
		$this->load->view("sales/register",$data);                
    }
    
    
    function edit_sale($customer_id)
    {
        $cate = $this->Sale->get_categories();

        $nInc = 0;
        foreach($cate->result() as $cate1)
        {
            if($nInc == 0)
                $first_num = $cate1->category_id;
            $nInc ++;    
        }
        $bInsert = $this->Sale->delete_all_temp();
        
        $delivery_orders_info = $this->Phone->get_temp_delivery_orders($customer_id);
        
        $sale_id = $delivery_orders_info->sale_id;
        
        if($sale_id == 0)
            $sale_id = -1;

        $sale_results = $this->Sale->get_sale_items($sale_id);
        foreach($sale_results->result() as $sale_res)
        {
            $bInsert = $this->Sale->insert_temp_data(1 , $sale_res->item_id , $sale_res->quantity_purchased);    
        }
        
        
        $data['categories'] = $this->Sale->get_categories();
        $data['sub_categories'] = $this->Sale->get_sub_categories($first_num);
        $data['items'] = get_sale_item_rows($this->Sale->get_sub_categories($first_num) , $this);
        $data['sales_table'] = get_delivery_sales_rows($this , "sales");
        $data['suspended_sales_table'] = get_delivery_suspended_sales_rows($this , "sales");
        $data['controller_name'] = strtolower("sales");
        $data['delivery_and_collect'] = 1;  //is delivery!
        
                


        $selected_table_items = get_refresh_table($this);
        list($selected_items_table , $total_amount) = explode("," , $selected_table_items);
        $data['selected_items_table'] = $selected_items_table;
        $data['total_amount'] = $total_amount;

        $amt_tendered = $this->Sale->get_payment_amount($sale_id);
        $data['amt_tendered'] = $amt_tendered;
        $data['change_amount'] = $amt_tendered - $total_amount;        


        $data['sale_id'] = $sale_id;
        $data['customer_id'] = $customer_id;

        
        if($customer_id!=-1)
		{
			$info=$this->Customer->get_info($customer_id);
			$data['customer']=$info->first_name.' '.$info->last_name;
            
			$data['customer_email']=$info->email;
		}
        
        
        
        $data['sale_status'] = 1;  //sale_status is new!
        
		$this->load->view("sales/register",$data);                
    }


    function save_collect()
    {
/*        
        $phone_category = $this->input->post('phone_category');
        if($phone_category == 1)
        {
            $delivery_time = $this->input->post('delivery_time');
            $delivery_time = date("Y-m-d").$delivery_time;
                        
            $save_data = array(
                'contact_number'=>$this->input->post('contact_number'),
                'contact_address'=>$this->input->post('contact_address'),
                'post_code'=>$this->input->post('post_code'),
                'delivery_time'=>$delivery_time,
                'company_id'=>$this->input->post('company_id')
            );
            
            if($this->Phone->save($save_data , $phone_category))
                echo "OK";
            else
                echo "No";            
        }
        else if($phone_category == 2)
        {
            $collect_time = $this->input->post('collect_time');
            $collect_time = date("Y-m-d").$collect_time;  
            $save_data = array(
                'contact_number'=>$this->input->post('contact_number'),
                'collect_time'=>$collect_time,
                'company_id'=>$this->input->post('company_id')
            );                                     
            if($this->Phone->save($save_data , $phone_category))
                echo "OK";
            else
                echo "No";      
        }
*/        
    }

    
    function save_delivery()
    {
        $phone_category = $this->input->post('phone_category');
        
        if($phone_category == 1)
        {
            
            $delivery_time = $this->input->post('delivery_time');
            $delivery_time = date("Y-m-d").$delivery_time;
                        
            $save_data = array(
                'contact_number'=>$this->input->post('contact_number'),
                'contact_address'=>$this->input->post('contact_address'),
                'post_code'=>$this->input->post('post_code'),
                'delivery_time'=>$delivery_time,
                'company_id'=>$this->input->post('company_id')
            );
            
            if($this->Phone->save($save_data , $phone_category))
                echo "OK";
            else
                echo "No";            
        }
        else if($phone_category == 2)
        {
            $collect_time = $this->input->post('collect_time');
            $collect_time = date("Y-m-d").$collect_time;  
            $save_data = array(
                'contact_number'=>$this->input->post('contact_number'),
                'collect_time'=>$collect_time,
                'company_id'=>$this->input->post('company_id')
            );                                     
            if($this->Phone->save($save_data , $phone_category))
                echo "OK";
            else
                echo "No";      
        }
    }
    
    function validate_register()
    {
        $delivery_and_collect = $this->input->post('delivery_and_collect');
        
        if($delivery_and_collect == 1)
        {
            if($this->Phone->validate_temp_delivery_orders())
                echo "OK";
            else
                echo "No";    
        }
        else if($delivery_and_collect == 2)
        {
            if($this->Phone->validate_temp_collect_orders())
                echo "OK";
            else
                echo "No";    
            
        }
    }
    
    function cancel_delivery()
    {
        $delivery_and_collect = $this->input->post('delivery_and_collect');
        if($delivery_and_collect == 1)
        {
            if($this->Phone->cancel_delivery())
                echo "OK";
            else
                echo "No";    
        }
        else if($delivery_and_collect == 2)
        {
            if($this->Phone->cancel_collect())
                echo "OK";
            else
                echo "No";                
        }
    }
    
    function change_mode($mode)
    {
        if($mode == 1)
        {
            if(!$this->Phone->cancel_collect())
                echo "No";
            else
            {
        		$person_info = $this->Employee->get_logged_in_employee_info();
        		$data['companies'] = $this->Company->get_all(1000000 , 0);
                $company_id = 0;
                //$this->session->set_userdata('company_id' , $company_id);
        
                
                $data['delivery_info'] = $this->Phone->get_temp_delivery();
                
                $data['customer_table'] = get_phone_customer_manage_table($this , $company_id);
                $data['delivery_list'] = get_delivery_list($this , "phones");
                $data['controller_name'] = strtolower(get_class());
                $_SESSION['phone_category'] = 1;
                $data['phone_category'] = 1;
                $this->load->view("phone/phone",$data);                
            }    
        }
        
        if($mode == 2)
        {
            if(!$this->Phone->cancel_delivery())
                echo "No";
            else
            {    
        		$person_info = $this->Employee->get_logged_in_employee_info();
        		$data['companies'] = $this->Company->get_all(1000000 , 0);
                $company_id = 0;
                //$this->session->set_userdata('company_id' , $company_id);
        
                
                $data['collect_info'] = $this->Phone->get_temp_collect();
                
                $data['customer_table'] = get_phone_customer_manage_table($this , $company_id);
                $data['collect_list'] = get_collect_list($this , "phones");
                $data['controller_name'] = strtolower(get_class());
                $_SESSION['phone_category'] = 2;
                $data['phone_category'] = 2;
                $this->load->view("phone/phone",$data);
            }                
        }
    }
}
?>