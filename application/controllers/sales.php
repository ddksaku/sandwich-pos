<?php
require_once ("secure_area.php");
class Sales extends Secure_area
{
	function __construct()
	{
		parent::__construct('sales');
		$this->load->library('sale_lib');
	}

	function index()
	{
		$this->_reload();
	}

	function item_search()
	{
		$suggestions = $this->Item->get_item_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		$suggestions = array_merge($suggestions, $this->Item_kit->get_item_kit_search_suggestions($this->input->post('q'),$this->input->post('limit')));
		echo implode("\n",$suggestions);
	}

	function customer_search()
	{
		$suggestions = $this->Customer->get_customer_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}

	function select_customer()
	{
		$customer_id = $this->input->post("customer");
		$this->sale_lib->set_customer($customer_id);
		$this->_reload();
	}

	function change_mode()
	{
		$mode = $this->input->post("mode");
		$this->sale_lib->set_mode($mode);
		$this->_reload();
	}

	function set_comment()
	{
 	  $this->sale_lib->set_comment($this->input->post('comment'));
	}

	function set_email_receipt()
	{
 	  $this->sale_lib->set_email_receipt($this->input->post('email_receipt'));
	}

	//Alain Multiple Payments
	function add_payment()
	{
		$data = array();
		$this->form_validation->set_rules( 'amount_tendered', 'lang:sales_amount_tendered', 'numeric' );

		if ( $this->form_validation->run() == FALSE )
		{
			if ( $this->input->post( 'payment_type' ) == $this->lang->line( 'sales_gift_card' ) )
				$data['error']=$this->lang->line('sales_must_enter_numeric_giftcard');
			else
				$data['error']=$this->lang->line('sales_must_enter_numeric');

 			$this->_reload( $data );
 			return;
		}

		$payment_type = $this->input->post( 'payment_type' );
		if ( $payment_type == $this->lang->line( 'sales_giftcard' ) )
		{
			$payments = $this->sale_lib->get_payments();
			$payment_type = $this->input->post( 'payment_type' ) . ':' . $payment_amount = $this->input->post( 'amount_tendered' );
			$current_payments_with_giftcard = isset( $payments[$payment_type] ) ? $payments[$payment_type]['payment_amount'] : 0;
			$cur_giftcard_value = $this->Giftcard->get_giftcard_value( $this->input->post( 'amount_tendered' ) ) - $current_payments_with_giftcard;

			if ( $cur_giftcard_value <= 0 )
			{
				$data['error'] = 'Giftcard balance is ' . to_currency( $this->Giftcard->get_giftcard_value( $this->input->post( 'amount_tendered' ) ) ) . ' !';
				$this->_reload( $data );
				return;
			}

			$new_giftcard_value = $this->Giftcard->get_giftcard_value( $this->input->post( 'amount_tendered' ) ) - $this->sale_lib->get_amount_due( );
			$new_giftcard_value = ( $new_giftcard_value >= 0 ) ? $new_giftcard_value : 0;
			$data['warning'] = 'Giftcard ' . $this->input->post( 'amount_tendered' ) . ' balance is ' . to_currency( $new_giftcard_value ) . ' !';
			$payment_amount = min( $this->sale_lib->get_amount_due( ), $this->Giftcard->get_giftcard_value( $this->input->post( 'amount_tendered' ) ) );
		}
		else
		{
			$payment_amount = $this->input->post( 'amount_tendered' );
		}

		if( !$this->sale_lib->add_payment( $payment_type, $payment_amount ) )
		{
			$data['error']='Unable to Add Payment! Please try again!';
		}

		$this->_reload($data);
	}

	//Alain Multiple Payments
	function delete_payment( $payment_id )
	{
		$this->sale_lib->delete_payment( $payment_id );
		$this->_reload();
	}

	function add()
	{
		$data=array();
		$mode = $this->sale_lib->get_mode();
		$item_id_or_number_or_item_kit_or_receipt = $this->input->post("item");
		$quantity = $mode=="sale" ? 1:-1;

		if($this->sale_lib->is_valid_receipt($item_id_or_number_or_item_kit_or_receipt) && $mode=='return')
		{
			$this->sale_lib->return_entire_sale($item_id_or_number_or_item_kit_or_receipt);
		}
		elseif($this->sale_lib->is_valid_item_kit($item_id_or_number_or_item_kit_or_receipt))
		{
			$this->sale_lib->add_item_kit($item_id_or_number_or_item_kit_or_receipt);
		}
		elseif(!$this->sale_lib->add_item($item_id_or_number_or_item_kit_or_receipt,$quantity))
		{
			$data['error']=$this->lang->line('sales_unable_to_add_item');
		}

		if($this->sale_lib->out_of_stock($item_id_or_number_or_item_kit_or_receipt))
		{
			$data['warning'] = $this->lang->line('sales_quantity_less_than_zero');
		}
		$this->_reload($data);
	}

	function edit_item($line)
	{
		$data= array();

		$this->form_validation->set_rules('price', 'lang:items_price', 'required|numeric');
		$this->form_validation->set_rules('quantity', 'lang:items_quantity', 'required|numeric');

        $description = $this->input->post("description");
        $serialnumber = $this->input->post("serialnumber");
		$price = $this->input->post("price");
		$quantity = $this->input->post("quantity");
		$discount = $this->input->post("discount");


		if ($this->form_validation->run() != FALSE)
		{
			$this->sale_lib->edit_item($line,$description,$serialnumber,$quantity,$discount,$price);
		}
		else
		{
			$data['error']=$this->lang->line('sales_error_editing_item');
		}

		if($this->sale_lib->out_of_stock($this->sale_lib->get_item_id($line)))
		{
			$data['warning'] = $this->lang->line('sales_quantity_less_than_zero');
		}


		$this->_reload($data);
	}

	function delete_item($item_number)
	{
		$this->sale_lib->delete_item($item_number);
		$this->_reload();
	}

	function remove_customer()
	{
		$this->sale_lib->remove_customer();
		$this->_reload();
	}

	function complete()
	{
		$data['cart']=$this->sale_lib->get_cart();
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxes();
		$data['total']=$this->sale_lib->get_total();
		$data['receipt_title']=$this->lang->line('sales_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a');
		$customer_id=$this->sale_lib->get_customer();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$comment = $this->sale_lib->get_comment();
		$emp_info=$this->Employee->get_info($employee_id);
		$data['payments']=$this->sale_lib->get_payments();
		$data['amount_change']=to_currency($this->sale_lib->get_amount_due() * -1);
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name;
		}

		//SAVE sale to database
		$data['sale_id']='POS '.$this->Sale->save($data['cart'], $customer_id,$employee_id,$comment,$data['payments']);
		if ($data['sale_id'] == 'POS -1')
		{
			$data['error_message'] = $this->lang->line('sales_transaction_failed');
		}
		else
		{
			if ($this->sale_lib->get_email_receipt() && !empty($cust_info->email))
			{
				$this->load->library('email');
				$config['mailtype'] = 'html';
				$this->email->initialize($config);
				$this->email->from($this->config->item('email'), $this->config->item('company'));
				$this->email->to($cust_info->email);

				$this->email->subject($this->lang->line('sales_receipt'));
				$this->email->message($this->load->view("sales/receipt_email",$data, true));
				$this->email->send();
			}
		}
		$this->load->view("sales/receipt",$data);
		$this->sale_lib->clear_all();
	}

	function print_receipt($sale_id)
	{
        $sale_info = $this->Sale->get_info($sale_id)->row_array();
        $sale_items_info = $this->Sale->get_sale_items($sale_id);

        $selected_table_items = get_refresh_table($this);
        list($selected_items_table , $total_amount) = explode("," , $selected_table_items);

        $data['total_amount'] = $total_amount;
        $data['controller_name'] = strtolower(get_class());
        $customer_info = $this->Sale->get_customer($sale_id);
        $data['customer_id'] = $customer_info->person_id;
        $data['customer'] = $customer_info->first_name.' '.$customer_info->last_name;
        $data['customer_email']=$customer_info->email;
        $amt_tendered = $this->Sale->get_payment_amount($sale_id);
        $data['amt_tendered'] = $amt_tendered;
        $data['sale_id'] = $sale_id;
        $data['sale_status'] = 2;  //sale_status is completed!
        $data['change_amount'] = $amt_tendered - $total_amount;
        $data['sale_info'] = $sale_info;
        $data['sale_items_info'] = $sale_items_info;
        $data['receipt_title']=$this->lang->line('sales_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a', strtotime($sale_info['sale_time']));
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;
        $data['sale_id']='POS '.$sale_id;
        $data['change_amount'] = $amt_tendered - $total_amount;
        $this->load->view("sales/receipt",$data);
/*
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		$this->sale_lib->copy_entire_sale($sale_id);
		$data['cart']=$this->sale_lib->get_cart();
		$data['payments']=$this->sale_lib->get_payments();
		$data['subtotal']=$this->sale_lib->get_subtotal();
		//$data['taxes']=$this->sale_lib->get_taxes();
		$data['total']=$this->sale_lib->get_total();
		$data['receipt_title']=$this->lang->line('sales_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a', strtotime($sale_info['sale_time']));
		$customer_id=$this->sale_lib->get_customer();
		$emp_info=$this->Employee->get_info($sale_info['employee_id']);
		$data['payment_type']=$sale_info['payment_type'];
		$data['amount_change']=to_currency($this->sale_lib->get_amount_due() * -1);
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name;
		}
		$data['sale_id']='POS '.$sale_id;
		$this->load->view("sales/receipt",$data);
		$this->sale_lib->clear_all();
*/
	}

	function edit($sale_id)
	{
		$data = array();

		$data['customers'] = array('' => 'No Customer');
		foreach ($this->Customer->get_all()->result() as $customer)
		{
			$data['customers'][$customer->person_id] = $customer->first_name . ' '. $customer->last_name;
		}

		$data['employees'] = array();
		foreach ($this->Employee->get_all()->result() as $employee)
		{
			$data['employees'][$employee->person_id] = $employee->first_name . ' '. $employee->last_name;
		}

		$data['sale_info'] = $this->Sale->get_info($sale_id)->row_array();


		$this->load->view('sales/edit', $data);
	}

	function delete($sale_id)
	{
		$data = array();

		if ($this->Sale->delete($sale_id))
		{
			$data['success'] = true;
		}
		else
		{
			$data['success'] = false;
		}

		$this->load->view('sales/delete', $data);

	}


    function refund()
    {
        $customer_id = $this->input->post('customer');
        $sale_id = $this->input->post('sale_id');
        $total_amount = $this->input->post('total_amount');
        $payment_type = $this->input->post('payment_type');
        $delivery_and_collect = $this->input->post('delivery_and_collect');
        $suspended = $this->input->post('suspended');
        $bSuccess = $this->Sale->get_enable_refund($customer_id , $total_amount);
        if(!$bSuccess)
            echo "false";
        else
        {
            $sale_time = date('Y-m-d');
            $sale_time .= " ";
            $sale_time .= date('H:i:s');
            $sale_data = array(
                'sale_time'=>$sale_time,
                'customer_id'=>$customer_id,
                'suspended'=>$suspended,
//                'employee_id'=>$this->input->post('employee_id'),
//                'comment'=>$this->input->post('comment'),
                'payment_type'=>$payment_type
            );
            $bInsert = $this->Sale->update($sale_data, $sale_id , $total_amount);
            if(!$bInsert) echo "false";
            else
            {
                if($delivery_and_collect == 1)
                {
                    $this->Phone->save_delivery_orders_temp($customer_id , $sale_id);
                    echo $delivery_and_collect;
                    //$this->load_delivery();
                }
                else if($delivery_and_collect == 2)
                {
                    $this->Phone->save_collect_orders_temp($customer_id , $sale_id);
                    echo $delivery_and_collect;
                }
                else
                    echo $delivery_and_collect;
            }
         }
    }

	function save()
	{
	   $customer_id = $this->input->post('customer');
       $sale_id = $this->input->post('sale_id');
       $amt_tendered = $this->input->post('amt_tendered');
       $payment_type = $this->input->post('payment_type');
       $delivery_and_collect = $this->input->post('delivery_and_collect');
       $suspended = $this->input->post('suspended');
       $sale_time = date('Y-m-d');
       $sale_time .= " ";
       $sale_time .= date('H:i:s');

       if($sale_id == 0)
            $sale_id = -1;

       //if($customer_id == -1) $customer_id = 0;
//       if($amt_tendered == "") $amt_tendered = 0;

       $sale_data = array(
            'sale_time'=>$sale_time,
            'customer_id'=>$customer_id,
            'suspended'=>$suspended,
//            'employee_id'=>$this->input->post('employee_id'),
//            'comment'=>$$this->input->post('comment'),
            'payment_type'=>$payment_type
       );

       $bInsert = $this->Sale->update($sale_data, $sale_id , $amt_tendered);
       //$bInsert = $this->Sale->delete_all_temp1();
       if($delivery_and_collect == 1)
       {
            $this->Phone->save_delivery_orders_temp($customer_id , $sale_id);
            echo $delivery_and_collect;
            //$this->load_delivery();
       }
       else if($delivery_and_collect == 2)
       {
            $this->Phone->save_collect_orders_temp($customer_id , $sale_id);
            echo $delivery_and_collect;
       }
       echo $delivery_and_collect;

	}



    function load_collect()
    {
		$person_info = $this->Employee->get_logged_in_employee_info();

		$data['person_info'] = $person_info;
		$data['companies'] = $this->Company->get_all(1000000 , 0);

        //$this->session->set_userdata('company_id' , $company_id);

        $data['collect_info'] = $this->Phone->get_temp_collect();

        $data['customer_table'] = get_phone_customer_manage_table($this , $company_id);
        $data['collect_list'] = get_collect_list($this , "phones");
        $data['controller_name'] = strtolower("phones");
        //$_SESSION['phone_category'] = 1;
        $data['phone_category'] = 2;
        $this->load->view("phone/phone",$data);
    }



    function load_delivery()
    {
		$person_info = $this->Employee->get_logged_in_employee_info();

		$data['person_info'] = $person_info;
		$data['companies'] = $this->Company->get_all(1000000 , 0);

        //$this->session->set_userdata('company_id' , $company_id);

        $data['delivery_info'] = $this->Phone->get_temp_delivery();

        $data['customer_table'] = get_phone_customer_manage_table($this , $company_id);
        $data['delivery_list'] = get_delivery_list($this , "phones");
        $data['controller_name'] = strtolower("phones");
        //$_SESSION['phone_category'] = 1;
        $data['phone_category'] = 1;
        $this->load->view("phone/phone",$data);
    }



	function _payments_cover_total()
	{
		$total_payments = 0;

		foreach($this->sale_lib->get_payments() as $payment)
		{
			$total_payments += $payment['payment_amount'];
		}

		/* Changed the conditional to account for floating point rounding */
		if ( ( $this->sale_lib->get_mode() == 'sale' ) && ( ( to_currency_no_money( $this->sale_lib->get_total() ) - $total_payments ) > 1e-6 ) )
		{
			return false;
		}

		return true;
	}

    function load_sale($sale_id)
    {
        //$sale_id = $this->input->post('sale_id');
		$person_info = $this->Employee->get_logged_in_employee_info();
        $cate = $this->Sale->get_categories();

        foreach($cate->result() as $cate1)
        {
            if($nInc == 0)
                $first_num = $cate1->category_id;
            $nInc ++;
        }

        if(!$this->Sale->delete_all_temp()) return false;
        if(!$this->Sale->delete_all_temp1()) return false;

        $sale_results = $this->Sale->get_sale_items($sale_id);
        foreach($sale_results->result() as $sale_res)
        {
            $bInsert = $this->Sale->insert_temp1_data(1 , $sale_res->item_id , $sale_res->quantity_purchased);

        }

        $data['person_info'] = $person_info;
        $data['categories'] = $this->Sale->get_categories();
        $data['sub_categories'] = $this->Sale->get_sub_categories($first_num);
        $data['items'] = get_sale_item_rows($this->Sale->get_sub_categories($first_num) , $this);
        $data['sales_table'] = get_sales_rows($this);
        $data['suspended_sales_table'] = get_suspended_sales_rows($this);
        $selected_table_items = get_refresh_table($this);
        list($selected_items_table , $total_amount) = explode("," , $selected_table_items);
        $data['selected_items_table'] = $selected_items_table;
        $data['total_amount'] = $total_amount;
        $data['controller_name'] = strtolower(get_class());
        $customer_info = $this->Sale->get_customer($sale_id);
        $data['customer_id'] = $customer_info->person_id;
    	$data['customer'] = $customer_info->first_name.' '.$customer_info->last_name;
		$data['customer_email']=$customer_info->email;
        $amt_tendered = $this->Sale->get_payment_amount($sale_id);
        $data['amt_tendered'] = $amt_tendered;
        $data['sale_id'] = $sale_id;
        $data['delivery_and_collect'] = 3;  //is only sale!
        $data['sale_status'] = 2;  //sale_status is completed!
        $data['change_amount'] = $amt_tendered - $total_amount;
		$this->load->view("sales/register",$data);
    }

    function load_suspend_sale($sale_id)
    {
        //$sale_id = $this->input->post('sale_id');
		$person_info = $this->Employee->get_logged_in_employee_info();
        $cate = $this->Sale->get_categories();

        foreach($cate->result() as $cate1)
        {
            if($nInc == 0)
                $first_num = $cate1->category_id;
            $nInc ++;
        }

        if(!$this->Sale->delete_all_temp()) return false;
        if(!$this->Sale->delete_all_temp1()) return false;

        $sale_results = $this->Sale->get_suspend_sale_items($sale_id);
        foreach($sale_results->result() as $sale_res)
        {
            $bInsert = $this->Sale->insert_temp1_data(1 , $sale_res->item_id , $sale_res->quantity_purchased);
        }
        $data['person_info'] = $person_info;
        $data['categories'] = $this->Sale->get_categories();
        $data['sub_categories'] = $this->Sale->get_sub_categories($first_num);
        $data['items'] = get_sale_item_rows($this->Sale->get_sub_categories($first_num) , $this);
        $data['sales_table'] = get_sales_rows($this);
        $data['suspended_sales_table'] = get_suspended_sales_rows($this);
        $selected_table_items = get_refresh_table($this);
        list($selected_items_table , $total_amount) = explode("," , $selected_table_items);
        $data['selected_items_table'] = $selected_items_table;
        $data['total_amount'] = $total_amount;
        $data['controller_name'] = strtolower(get_class());
        $customer_info = $this->Sale->get_suspend_customer($sale_id);
        if($customer_info == -1)
            $data['customer_id'] = $customer_info;//$customer_info->person_id;
        else
        {
            $data['customer_id'] = $customer_info->person_id;
    	    $data['customer'] = $customer_info->first_name.' '.$customer_info->last_name;
		    $data['customer_email']=$customer_info->email;
        }
        $data['sale_status'] = 3;  //sale_status is suspended!
        $data['suspend_sale_id'] = $sale_id;
        $data['delivery_and_collect'] = 3;  //is only sale!
        $amt_tendered = $this->Sale->get_payment_amount($sale_id);
        $data['amt_tendered'] = $amt_tendered;
        $data['change_amount'] = $amt_tendered - $total_amount;
		$this->load->view("sales/register",$data);
    }

	function _reload($data=array())
	{
		$person_info = $this->Employee->get_logged_in_employee_info();
        $cate = $this->Sale->get_categories();

        $nInc = 0;
        foreach($cate->result() as $cate1)
        {
            if($nInc == 0)
                $first_num = $cate1->category_id;
            $nInc ++;
        }

        $bInsert = $this->Sale->delete_all_temp();
        $bInsert = $this->Sale->delete_all_temp1();

        $data['person_info'] = $person_info;
        $data['categories'] = $this->Sale->get_categories();
        $data['sub_categories'] = $this->Sale->get_sub_categories($first_num);
        $data['items'] = get_sale_item_rows($this->Sale->get_sub_categories($first_num) , $this);
        $data['sales_table'] = get_sales_rows($this);
        $data['suspended_sales_table'] = get_suspended_sales_rows($this);
        $data['controller_name'] = strtolower(get_class());
        $data['delivery_and_collect'] = 3;  //is only sale!
        //$data['sale_id'] = $this->sale_lib->get_s
        $customer_id=$this->sale_lib->get_customer();
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

    function get_item_kit_table()
    {
        $mod = $this->input->post('mod');
        $item_id = $this->input->post('item_id');
        $bInsert = $this->Sale->insert_temp_data($mod , $item_id , 1);
        $data_table = get_item_kit_rows($this);
        echo $data_table;
    }

    function get_item_kit_list()
    {
        $responseData = "";
        $item_kit_id = $this->input->post('item_kit');

        $results = $this->Sale->item_kit_items_list1($item_kit_id);
        $nCount = 0;

        if($results->num_rows() == 0 || $results->num_rows() > 1) return false;

        $res = $results->row();
        $bInsert = $this->Sale->insert_temp_data(1 , $res->item_id , 1);
//        $responseData = $res->item_id;

        $results = $this->Sale->item_kit_items_list($item_kit_id);

        foreach($results->result() as $res)
        {
            if($nCount != 0)
                $responseData .= ",";
            $bInsert = $this->Sale->insert_temp_data(0 , $res->item_id , $res->quantity);
            $responseData .= $res->item_id;
            $nCount ++;
        }

        echo $responseData;
    }


    function cancel_sale()
    {
    	$this->sale_lib->clear_all();
    	$this->_reload();

    }

	function suspend()
	{
	   $customer_id = $this->input->post('customer');
       $sale_id = $this->input->post('sale_id');
       $sale_time = date('Y-m-d');
       $sale_time .= " ";
       $sale_time .= date('H:i:s');

       $sale_data = array(
            'sale_time'=>$sale_time,
            'customer_id'=>$customer_id,
//            'employee_id'=>$this->input->post('employee_id'),
//            'comment'=>$this->input->post('comment')
       );

       $bInsert = $this->Sale->suspend($sale_data, $sale_id);
/*
		$data['transaction_time']= date('m/d/Y h:i:s a');
		$customer_id=$this->sale_lib->get_customer();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$emp_info=$this->Employee->get_info($employee_id);
		$payment_type = $this->input->post('payment_type');

		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name;
		}

		$total_payments = 0;

		foreach($data['payments'] as $payment)
		{
			$total_payments += $payment['payment_amount'];
		}

		//SAVE sale to database
		$data['sale_id']='POS '.$this->Sale_suspended->save($data['cart'], $customer_id,$employee_id,$comment,$data['payments']);
		if ($data['sale_id'] == 'POS -1')
		{
			$data['error_message'] = $this->lang->line('sales_transaction_failed');
		}
		$this->sale_lib->clear_all();
		$this->_reload(array('success' => $this->lang->line('sales_successfully_suspended_sale')));
*/
	}

	function suspended()
	{
		$data = array();
		$data['suspended_sales'] = $this->Sale_suspended->get_all()->result_array();
		$this->load->view('sales/suspended', $data);
	}

	function unsuspend()
	{
		$sale_id = $this->input->post('suspended_sale_id');
		$this->sale_lib->clear_all();
		$this->sale_lib->copy_entire_suspended_sale($sale_id);
		$this->Sale_suspended->delete($sale_id);
    	$this->_reload();
	}


    function replace_table1()
    {
        $category_id = $this->input->post('category_id');
        $this->Sale->set_sale_temp1();
        $table_data = "<table cellspacing='0px' style=' -moz-border-radius : 4px; -webkit-border-radius : 4px; border-radius : 4px; background-color: #cccccc; text-align: center;'>";
        $table_data .= "<tr ><td><table cellspacing='0px'>";
        $sub_categories = $this->Sale->get_sub_categories($category_id);
        $table_data .= get_slae_sub_categories_row($sub_categories);
        $table_data .= "</tr></table></td></tr></table>";
        $table_data .= "<table cellspacing='0px' style=' -moz-border-radius : 4px; -webkit-border-radius : 4px;
                        border-radius : 4px; background-color: #ffffff; text-align: center;'><tr>";
        $table_data .= get_sale_item_rows($sub_categories , $this);
        $table_data .= "</tr></table>";
        //echo json_encode(array('success'=>true,'responseText'=>$table_data));
        echo $table_data;
    }


    function replace_table()
    {
        $category_id = $this->input->post('category_id');
        //$this->Sale->delete_all_temp();
        $table_data = "<table cellspacing='0px' style=' -moz-border-radius : 4px; -webkit-border-radius : 4px; border-radius : 4px; background-color: #cccccc; text-align: center;'>";
        $table_data .= "<tr ><td><table cellspacing='0px'>";
        $sub_categories = $this->Sale->get_sub_categories($category_id);
        $table_data .= get_slae_sub_categories_row($sub_categories);
        $table_data .= "</tr></table></td></tr></table>";
        $table_data .= "<table cellspacing='0px' style=' -moz-border-radius : 4px; -webkit-border-radius : 4px;
                        border-radius : 4px; background-color: #ffffff; text-align: center;'><tr>";
        $table_data .= get_sale_item_rows($sub_categories , $this);
        $table_data .= "</tr></table>";
        //echo json_encode(array('success'=>true,'responseText'=>$table_data));
        echo $table_data;

    }

    function refresh_item_table()
    {
        $table_data = get_refresh_table($this);
        echo $table_data;
    }

    function inc_dec_item_table()
    {
        $mod = $this->input->post('mod');
        $item = $this->input->post('item');

        $bUpdate = $this->Sale->inc_dec_item($mod , $item);
        echo $bUpdate;
    }


    function delete_item_row()
    {
        $item_id = $this->input->post('item_id');
        $bIs = $this->Sale->insert_temp1_data(0 , $item_id , 1);
        $data1 = $this->Sale->get_all_items_id();
        $data2 = $this->Sale->get_all_temp1_items();

        $data1 .= "*****";
        $data1 .= $data2;
        echo $data1;
    }


    function view_order()
    {
    	$isCompleted = $this->input->post('isCompleted');
    	if($isCompleted == 1)
    		echo get_sales_rows($this);
    	else if($isCompleted == 2)
    		echo get_suspended_sales_rows($this);
    }

    function load_phone_orders()
    {
		redirect("phones");
	}


	function load_delivery_orders()
	{
		redirect("deliveries");
	}


	function load_view_orders()
	{
		redirect("vieworders");
	}

}
?>