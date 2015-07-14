<?php
/*
Gets the html table to manage people.
*/
function get_people_manage_table($people,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';

	$headers = array('<input type="checkbox" id="select_all" />',
	$CI->lang->line('common_last_name'),
	$CI->lang->line('common_first_name'),
	$CI->lang->line('common_email'),
	$CI->lang->line('common_phone_number'),
//    $CI->lang->line('common_deposit'),
	'&nbsp');

	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_people_manage_table_data_rows($people,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the people.
*/
function get_people_manage_table_data_rows($people,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($people->result() as $person)
	{
		$table_data_rows.=get_person_data_row($person,$controller);
	}

	if($people->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='7'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('common_no_persons_to_display')."</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_person_data_row($person,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='5%'><input type='checkbox' id='person_$person->person_id' value='".$person->person_id."'/></td>";
	$table_data_row.='<td width="20%">'.character_limiter($person->last_name,13).'</td>';
	$table_data_row.='<td width="20%">'.character_limiter($person->first_name,13).'</td>';
	$table_data_row.='<td width="20%">'.mailto($person->email,character_limiter($person->email,22)).'</td>';
	$table_data_row.='<td width="20%">'.character_limiter($person->phone_number,13).'</td>';
//    $table_data_row.='<td width="10%">'.character_limiter($person->deposit,10).'</td>';
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$person->person_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
	$table_data_row.='</tr>';

	return $table_data_row;
}

/*
Gets the html table to manage suppliers.
*/
function get_supplier_manage_table($suppliers,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';

	$headers = array('<input type="checkbox" id="select_all" />',
	$CI->lang->line('suppliers_company_name'),
	$CI->lang->line('common_last_name'),
	$CI->lang->line('common_first_name'),
	$CI->lang->line('common_email'),
	$CI->lang->line('common_phone_number'),
	'&nbsp');

	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_supplier_manage_table_data_rows($suppliers,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the supplier.
*/
function get_supplier_manage_table_data_rows($suppliers,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($suppliers->result() as $supplier)
	{
		$table_data_rows.=get_supplier_data_row($supplier,$controller);
	}

	if($suppliers->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='7'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('common_no_persons_to_display')."</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_supplier_data_row($supplier,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='5%'><input type='checkbox' id='person_$supplier->person_id' value='".$supplier->person_id."'/></td>";
	$table_data_row.='<td width="17%">'.character_limiter($supplier->company_name,13).'</td>';
	$table_data_row.='<td width="17%">'.character_limiter($supplier->last_name,13).'</td>';
	$table_data_row.='<td width="17%">'.character_limiter($supplier->first_name,13).'</td>';
	$table_data_row.='<td width="22%">'.mailto($supplier->email,character_limiter($supplier->email,22)).'</td>';
	$table_data_row.='<td width="17%">'.character_limiter($supplier->phone_number,13).'</td>';
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$supplier->person_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
	$table_data_row.='</tr>';

	return $table_data_row;
}



function get_companies_manage_table($companies , $controller , $sort_key = 1)
{
	$CI =& get_instance();
	$table='<table class="tablesorter_user" id="sortable_table">';

	$headers = array('<input type="checkbox" id="select_all" />',
    	$CI->lang->line('companies_name'),
    	$CI->lang->line('companies_contact_number'),
    	$CI->lang->line('companies_address'),
    	$CI->lang->line('companies_post_code'),
    	'&nbsp;'
	);
	$table.='<thead><tr>';
	$nCount = 1;
	$nCount2 = 0;
	foreach($headers as $header)
	{
		if($header == '&nbsp;')
		{
			$table .= "<th>".$header."</th>";
			$nCount2 ++;
			continue;
		}

		if($nCount2 == 0)
		{
			$table .= "<th>".$header."</th>";
			$nCount2 ++;
			continue;
		}

		$nCount1 = $nCount + 1;
		if($nCount == $sort_key)
			$table .= "<th class='headerSortDown' onclick='sort_product(this);'>".$header."</th>";
		else if($nCount1 == $sort_key)
			$table .= "<th class='headerSortUp' onclick='sort_product(this);'>".$header."</th>";
		else if($nCount < 8)
			$table .= "<th class='header' onclick='sort_product(this);'>".$header."</th>";

		$nCount += 2;


//		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_companies_manage_table_data_rows($companies , $controller);
	$table.='</tbody></table>';
	return $table;
}

function get_companies_manage_table_data_rows($companies , $controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	$nCount = 0;

	foreach($companies->result() as $company)
	{
		$table_data_rows .= get_company_data_row($company , $controller , $nCount);
		$nCount ++;
	}

	if($companies->num_rows() == 0)
	{
		$table_data_rows .= "<tr><td colspan='6'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('companies_no_items_to_display')."</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_company_data_row($company , $controller , $nCount = 0)
{
	$CI =& get_instance();

	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	if($nCount % 2 == 0)
		$table_data_row = '<tr style="background-color:#E4E4FF;">';
	else
		$table_data_row = '<tr style="background-color:#FFFFFF;">';
	$table_data_row.="<td width='5%'><input type='checkbox' id='company_$company->company_id' value='".$company->company_id."'/></td>";
	$table_data_row.='<td width="20%">'.$company->name.'</td>';
	$table_data_row.='<td width="20%">'.$company->contact_number.'</td>';
	$table_data_row.='<td width="20%">'.$company->contact_address.'</td>';
	$table_data_row.='<td width="30%">'.$company->post_code.'</td>';
	$table_data_row .= '<td width="5%" style="text-align:center;">';
	$table_data_row .= "<div class='tiny_button' onmouseover='this.className=\"tiny_button_over\"' onmouseout='this.className=\"tiny_button\"' onclick='popup_dialog(".$company->company_id.");'><span>".$CI->lang->line('common_edit')."</span></div>";
	$table_data_row .= '</td>';

//	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$company->company_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
	$table_data_row.='</tr>';
	return $table_data_row;
}

/*
Gets the html table to manage items.
*/
function get_items_manage_table($items,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';

	$headers = array('<input type="checkbox" id="select_all" />',
//	$CI->lang->line('items_item_number'),
	$CI->lang->line('items_name'),
	$CI->lang->line('items_category'),
	$CI->lang->line('items_cost_price'),
	$CI->lang->line('items_unit_price'),
//	$CI->lang->line('items_tax_percents'),
//	$CI->lang->line('items_quantity'),
	'&nbsp;',
//	$CI->lang->line('items_inventory')
	);

	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_items_manage_table_data_rows($items,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the items.
*/
function get_items_manage_table_data_rows($items,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($items->result() as $item)
	{
		$table_data_rows.=get_item_data_row($item,$controller);
	}

	if($items->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='11'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('items_no_items_to_display')."</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_item_data_row($item,$controller)
{
	$CI =& get_instance();
	$item_tax_info=$CI->Item_taxes->get_info($item->item_id);
	$tax_percents = '';
	foreach($item_tax_info as $tax_info)
	{
		$tax_percents.=$tax_info['percent']. '%, ';
	}
	$tax_percents=substr($tax_percents, 0, -2);
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='3%'><input type='checkbox' id='item_$item->item_id' value='".$item->item_id."'/></td>";
	$table_data_row.='<td width="20%">'.$item->name.'</td>';
	$table_data_row.='<td width="14%">'.$item->category.'</td>';
	$table_data_row.='<td width="14%">'.to_currency($item->cost_price).'</td>';
	$table_data_row.='<td width="14%">'.to_currency($item->unit_price).'</td>';
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$item->item_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
	$table_data_row.='</tr>';
	return $table_data_row;
}

/*
Gets the html table to manage giftcards.
*/
function get_giftcards_manage_table( $giftcards, $controller )
{
	$CI =& get_instance();

	$table='<table class="tablesorter" id="sortable_table">';

	$headers = array('<input type="checkbox" id="select_all" />',
	$CI->lang->line('giftcards_giftcard_number'),
	$CI->lang->line('giftcards_card_value'),
	'&nbsp',
	);

	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_giftcards_manage_table_data_rows( $giftcards, $controller );
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the giftcard.
*/
function get_giftcards_manage_table_data_rows( $giftcards, $controller )
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($giftcards->result() as $giftcard)
	{
		$table_data_rows.=get_giftcard_data_row( $giftcard, $controller );
	}

	if($giftcards->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='11'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('giftcards_no_giftcards_to_display')."</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_giftcard_data_row($giftcard,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='3%'><input type='checkbox' id='giftcard_$giftcard->giftcard_id' value='".$giftcard->giftcard_id."'/></td>";
	$table_data_row.='<td width="15%">'.$giftcard->giftcard_number.'</td>';
	$table_data_row.='<td width="20%">'.to_currency($giftcard->value).'</td>';
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$giftcard->giftcard_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';

	$table_data_row.='</tr>';
	return $table_data_row;
}


/*
Gets the html table to manage item kits.
*/
function get_item_kits_manage_table( $item_kits, $controller )
{
	$CI =& get_instance();

	$table='<table class="tablesorter" id="sortable_table">';

	$headers = array('<input type="checkbox" id="select_all" />',
	$CI->lang->line('item_kits_name'),
	$CI->lang->line('item_kits_description'),
	'&nbsp',
	);

	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_item_kits_manage_table_data_rows( $item_kits, $controller );
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the item kits.
*/
function get_item_kits_manage_table_data_rows( $item_kits, $controller )
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($item_kits->result() as $item_kit)
	{
		$table_data_rows.=get_item_kit_data_row( $item_kit, $controller );
	}

	if($item_kits->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='11'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('item_kits_no_item_kits_to_display')."</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_item_kit_data_row($item_kit,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='3%'><input type='checkbox' id='item_kit_$item_kit->item_id' value='".$item_kit->item_id."'/></td>";
	$table_data_row.='<td width="15%">'.$item_kit->name.'</td>';
	$table_data_row.='<td width="20%">'.character_limiter($item_kit->description, 25).'</td>';
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$item_kit->item_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';

	$table_data_row.='</tr>';
	return $table_data_row;
}



/*
Gets the html table to manage items.
*/
function get_categories_manage_table($categories,$controller,$bMakeHeader)
{
	$CI =& get_instance();
    if($bMakeHeader)
    {
    	$table='<table class="tablesorter" id="sortable_table">';

    	$headers = array('<input type="checkbox" id="select_all" />',
    	$CI->lang->line('categories_name'),
        '&nbsp',
    	$CI->lang->line('categories_sub_category_name'),
    	'&nbsp;',
        '&nbsp;',
    	);

    	$table.='<thead><tr>';
    	foreach($headers as $header)
    	{
    		$table.="<th style='text-align:center;'>$header</th>";
    	}
    	$table.='</tr></thead><tbody>';
    	$table.=get_categories_manage_table_data_rows($categories,$controller);
    	$table.='</tbody></table>';
    }
    else
    {
        $table = "<tbody>";
        $table.= get_categories_manage_table_data_rows($categories,$controller);
        $table.='</tbody>';
    }
	return $table;
}

/*
Gets the html data rows for the items.
*/
function get_categories_manage_table_data_rows($categories,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($categories->result() as $category)
	{
        $table_data_rows.=get_category_data_row($category,$controller);
	}
//

	if($categories->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='3'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('categories_no_categories_to_display')."</div></tr></tr>";
	}

	return $table_data_rows;
}


function get_category_data_row($category,$controller)
{

	$CI =& get_instance();

	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

    $sub_categories = $controller->Category->get_sub_categories($category->category_id);

    $sub_num_rows = $sub_categories->num_rows();
//    return "<tr><td>$sub_num_rows</td></tr>";

	$table_data_row='<tr>';
	$table_data_row.="<td width='3%' style='border:solid 1px gray; vertical-align:middle; text-align:center;' rowspan='".$sub_num_rows."'><input type='checkbox' id='category_$category->category_id' value='".$category->category_id."'/></td>";
    if($sub_num_rows != 0)
    {
	   $table_data_row.='<td width="35%" style="border:solid 1px gray; text-align:center; font-size:20px; vertical-align:middle;" rowspan="'.$sub_num_rows.'">'.$category->name.'</td>';

       $nCount = 0;
       foreach($sub_categories->result() as $sub_category)
       {
            if($nCount == 0)
            {
                $table_data_row.="<td width='3%' style='border:solid 1px gray; text-align:center;'><input type='checkbox' id='category_$sub_category->category_id' value='".$sub_category->category_id."' /></td>";
                $table_data_row.="<td width='15%' style='border:solid 1px gray; text-align:center;'>".$sub_category->name."</td>";
                $table_data_row.='<td width="5%" style="border:solid 1px gray; text-align:center;">'.anchor($controller_name."/view/$sub_category->category_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
                $table_data_row.='<td width="5%" style="border:solid 1px gray; text-align:center; font-size:20px; vertical-align:middle;" rowspan="'.$sub_num_rows.'">'.anchor($controller_name."/view/$category->category_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
                $table_data_row.="</tr>";
            }
            else
            {
                $table_data_row.="<tr>";
                $table_data_row.="<td width='3%' style='border:solid 1px gray; text-align:center;'><input type='checkbox' id='category_$sub_category->category_id' value='".$sub_category->category_id."' /></td>";
                $table_data_row.="<td width='15%' style='border:solid 1px gray; text-align:center;'>".$sub_category->name."</td>";
                $table_data_row.='<td width="5%" style="border:solid 1px gray; text-align:center;">'.anchor($controller_name."/view/$sub_category->category_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
                $table_data_row.="</tr>";
            }
            $nCount ++;
       }

    }
    else
    {
       $table_data_row.='<td width="15%" style="border:solid 1px gray; text-align:center; font-size:20px; vertical-align:middle;">'.$category->name.'</td><td colspan="3" style="border:solid 1px gray; text-align:center;">'.$CI->lang->line($controller_name.'_cannot_find_sub_category').'</td>';
       $table_data_row.='<td width="5%" style="border:solid 1px gray; font-size:20px; text-align:center;">'.anchor($controller_name."/view/$category->category_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
       $table_data_row.='</tr>';
    }

	return $table_data_row;
}


function get_slae_sub_categories_row($sub_categories)
{
    $table_data = "";
    foreach($sub_categories->result() as $sub_category)
    {
        $table_data .= "<td class='subcategory' style='background-color: #8ecd48; '>$sub_category->name</td>";
    }

    $num_rows = $sub_categories->num_rows();
    for($nCount = $num_rows ; $nCount < 10 ; $nCount ++)
    {
    	$table_data .= "<td class='empty_subcategory' style='background-color: #6bbb7e'>&nbsp;</td>";
    }
    return $table_data;

}



function get_delivery_sales_rows($controller , $controller_name)
{
    $CI =& get_instance();
	//$controller_name=strtolower(get_class($CI));
    $table_data = "<table style='width: 100%;' cellspacing='1'><thead><tr><th style='width:50%; background-color: #11ccdd;'>Customer name</th><th style='width:50%; background-color: #11ccdd;'>Completed time</th></tr></thead><tbody>";
    $nCount = 0;


    $results = $controller->Sale->get_all_sale_result('ospos_sales');
    if($results->num_rows() == 0)
    {
        $table_data .= "<tr><td colspan='2' style='color: #aa3333; text-align: center;'>No orders to display</td></tr>";
    }
    else
    {
        foreach($results->result() as $res)
        {
            $customer_id = $res->customer_id;
            $info = $controller->Customer->get_info($customer_id);
			$customer = $info->first_name.' '.$info->last_name;
            $load_sale_url = site_url("$controller_name/load_sale/$res->sale_id");
            if($nCount % 2 == 0)
                $table_data .= "<tr style='background-color:#cccccc;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#cccccc');\"><td style='width:50%; text-align:center;'><span><a href='$load_sale_url'>";
            else
                $table_data .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td style='width:50%; text-align:center;'><span><a href='$load_sale_url'>";

            $table_data .= $customer;
            $table_data .= "</a></span></td><td style='width:50%; text-align:center;'><span><a href='$load_sale_url'>";
            $table_data .= $res->sale_time;
            $table_data .= "</a></span></td></tr>";
            $nCount ++;
        }
    }

    $table_data .= "</tbody></table>";

    return $table_data;
}



function get_sales_rows($controller)
{
    $CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
    $table_data = "<table style='width: 100%;' cellspacing='1'><thead><tr><th style='width:50%; background-color: #11ccdd;'>Customer name</th><th style='width:50%; background-color: #11ccdd;'>Completed time</th></tr></thead><tbody>";
    $nCount = 0;


    $results = $controller->Sale->get_all_sale_result(0);
    if($results->num_rows() == 0)
    {
        $table_data .= "<tr><td colspan='2' style='color: #aa3333; text-align: center; font-size: 20px ;'>No orders to display</td></tr>";
    }
    else
    {
        foreach($results->result() as $res)
        {
            $customer_id = $res->customer_id;
            $info = $controller->Customer->get_info($customer_id);
			$customer = $info->first_name.' '.$info->last_name;
            $load_sale_url = site_url("$controller_name/load_sale/$res->sale_id");
            if($nCount % 2 == 0)
                $table_data .= "<tr style='background-color:#cccccc;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#cccccc');\"><td style='width:50%; text-align:center; font-size: 20px'><span><a href='$load_sale_url'>";
            else
                $table_data .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td style='width:50%; text-align:center; font-size: 20px'><span><a href='$load_sale_url'>";

            $table_data .= $customer;
            $table_data .= "</a></span></td><td style='width:50%; text-align:center; font-size: 20px;'><span><a href='$load_sale_url'>";
            $table_data .= $res->sale_time;
            $table_data .= "</a></span></td></tr>";
            $nCount ++;
        }
    }

    $table_data .= "</tbody></table>";

    return $table_data;
}


function get_delivery_suspended_sales_rows($controller , $controller_name)
{
    $CI =& get_instance();
//	$controller_name=strtolower(get_class($CI));
    $table_data = "<table style='width: 100%;' cellspacing='1'><thead><tr><th style='width:50%; background-color: #11ccdd;'>Customer name</th><th style='width:50%; background-color: #11ccdd;'>Suspended time</th></tr></thead><tbody>";
    $nCount = 0;


    $results = $controller->Sale->get_all_sale_result('ospos_sales_suspended');
    if($results->num_rows() == 0)
    {
        $table_data .= "<tr><td colspan='2' style='color: #aa3333; text-align: center;'>No orders to display</td></tr>";
    }
    else
    {
        foreach($results->result() as $res)
        {
            $customer_id = $res->customer_id;
            $info = $controller->Customer->get_info($customer_id);
			$customer = $info->first_name.' '.$info->last_name;
            $load_sale_url = site_url("$controller_name/load_suspend_sale/$res->sale_id");
            if($nCount % 2 == 0)
                $table_data .= "<tr style='background-color:#cccccc;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#cccccc');\"><td style='width:50%; text-align:center;'><span><a href='$load_sale_url'>";
            else
                $table_data .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td style='width:50%; text-align:center;'><span><a href='$load_sale_url'>";

            $table_data .= $customer;
            $table_data .= "</td><td style='width:50%; text-align:center;'><span><a href='$load_sale_url'>";
            $table_data .= $res->sale_time;
            $table_data .= "</td></tr>";
            $nCount ++;
        }
    }

    $table_data .= "</tbody></table>";

    return $table_data;
}




function get_suspended_sales_rows($controller)
{
    $CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
    $table_data = "<table style='width: 100%;' cellspacing='1'><thead><tr><th style='width:50%; background-color: #11ccdd;'>Customer name</th><th style='width:50%; background-color: #11ccdd;'>Suspended time</th></tr></thead><tbody>";
    $nCount = 0;


    $results = $controller->Sale->get_all_sale_result(1);
    if($results->num_rows() == 0)
    {
        $table_data .= "<tr><td colspan='2' style='color: #aa3333; text-align: center; font-size: 20px ;'>No orders to display</td></tr>";
    }
    else
    {
        foreach($results->result() as $res)
        {
            $customer_id = $res->customer_id;
            $info = $controller->Customer->get_info($customer_id);
			$customer = $info->first_name.' '.$info->last_name;
            $load_sale_url = site_url("$controller_name/load_suspend_sale/$res->sale_id");
            if($nCount % 2 == 0)
                $table_data .= "<tr style='background-color:#cccccc;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#cccccc');\"><td style='width:50%; text-align:center; font-size: 20px ;'><span><a href='$load_sale_url'>";
            else
                $table_data .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td style='width:50%; text-align:center; font-size: 20px ;'><span><a href='$load_sale_url'>";

            $table_data .= $customer;
            $table_data .= "</td><td style='width:50%; text-align:center; font-size: 20px ;'><span><a href='$load_sale_url'>";
            $table_data .= $res->sale_time;
            $table_data .= "</td></tr>";
            $nCount ++;
        }
    }

    $table_data .= "</tbody></table>";

    return $table_data;
}



function get_sale_item_rows($sub_categories , $controller)
{
    $CI =& get_instance();

	$controller_name=strtolower(get_class($CI));


    $table_data = "";

    foreach($sub_categories->result() as $sub_category)
    {
        $table_data .= "<td><table cellspacing='0px'>";
        $items = $controller->Sale->get_items($sub_category->category_id);
        if ($items == false)
            return false;
        foreach($items->result() as $item)
        {
            $bIs = $controller->Sale->get_sale_temp_item($item->item_id);
            if(!$bIs)
                $table_data .= "<tr><td class='item' id='item_$item->item_id' onclick='select_items($item->item_id);'>$item->name</td></tr>";
            else
                $table_data .= "<tr><td class='item_selected' id='item_$item->item_id' onclick='select_items($item->item_id);'>$item->name</td></tr>";

        }

        $num_rows = $items->num_rows();

        for($nCount = $num_rows ; $nCount < 12 ; $nCount ++)
        {
            $table_data .= "<tr><td class='empty_item'>&nbsp;</td></tr>";
        }

        $table_data .= "</table></td>";
    }

    $num_rows = $sub_categories->num_rows();
    for($nCount = $num_rows ; $nCount < 10 ; $nCount ++)
    {
        $table_data .= "<td><table cellspacing='0px'>";
        for($nCount1 = 0 ; $nCount1 < 12 ; $nCount1 ++)
        {
            $table_data .= "<tr><td class='empty_item'>&nbsp;</td></tr>";
        }

        $table_data .= "</table></td>";

    }
    return $table_data;
}


function get_item_kit_rows($controller)
{
    $table_data = "<table style='width:100%;'><thead><tr><th style='width:30%; background-color: #11ccdd;'>item kit name</th><th style='width:70%; background-color: #11ccdd;'>description</th></tr></thead>";

    $kit_items = $controller->Sale->kit_item_names();

    $nCount = 0;

    if($kit_items != false)
    {
    //return $kit_items;
        foreach($kit_items->result() as $kit_names)
        {
            $kit_item = $controller->Sale->kit_name($kit_names->item_kit_id);
            foreach($kit_item->result() as $result_kit_item)
            {
                if($nCount % 2 == 0)
                    $table_data .= "<tr onclick='highlight_kit_items($result_kit_item->item_id)' onmouseover='' onmouseout='' style='background-color:#cccccc;'><td style='width:30%; font-size: 20px'>$result_kit_item->name</td><td style='width:70%; font-size: 20px'>$result_kit_item->description</td></tr>";
                else
                    $table_data .= "<tr onclick='highlight_kit_items($result_kit_item->item_id)' onmouseover='' onmouseout='' style='background-color:#ffffff;'><td style='width:30%; font-size: 20px'>$result_kit_item->name</td><td style='width:70%; font-size: 20px'>$result_kit_item->description</td></tr>";

                $nCount ++;
            }
        }
    }
    else
        $table_data .= "<tbody><tr><td colspan='2' style='color: #aa3333; text-align: center; font-size: 20px;'>No item kits to display</td></tr></tbody>";

    $table_data .= "</table>";

    return $table_data;
/*
"    select kit_item_id from tb_kit_item
where item_id = 1 or item_id = 2
group by kit_item_id
having count(*) = 2";
*/
}

function get_refresh_table($controller)
{
    $table_data = "<table style='width:100%;' cellspacing='1'>
    				<thead>
    					<tr>
    						<th style='width: 30%; background-color: #11ccdd;'>Item</th>
    						<th style='width: 10%; background-color: #11ccdd;'>Quantity</th>
    						<th style='width: 10%; background-color: #11ccdd;'>Cost</th>
    						<th style='width: 10%; background-color: #11ccdd;'>&nbsp;</th>
    						<th style='width: 10%; background-color: #11ccdd;'>&nbsp;</th>
    						<th style='width: 15%; background-color: #11ccdd;'>&nbsp;</th>
    						<th style='width: 15%; background-color: #11ccdd;'>&nbsp;</th>
    					</tr>
    				</thead>";
    $total_amount = 0;
    $results = $controller->Sale->get_refresh_table();
    $nCount = 0;
    if($results->num_rows != 0)
    {
        foreach($results->result() as $res)
        {
            if($nCount % 2 == 0)
            {
                $cost = $res->cost_price * $res->quantity;
                $table_data .= "<tr style='background-color:#cccccc;' onmouseover='' onmouseout=''>
                					<td style='width:30%; text-align:center; font-size: 20px;'>$res->name</td>
                					<td style='width:10%; text-align:right; font-size: 20px;'>$res->quantity</td>
                					<td style='width:10%; text-align:right; font-size: 20px;'>";
                $table_data .= to_currency($res->cost_price);
                $table_data .= "</td>
                				<td onclick='incQuantity(this);'  style='width:10%; text-align:center; font-size: 20px; cursor: pointer;'>+</td>
                				<td onclick='decQuantity(this)' style='width:10%; text-align:center; font-size: 20px; cursor: pointer;'>-</td>";
                if($controller->Sale->is_item_kit($res->item_id))
                	$table_data .= "<td onclick='editItemRow($res->item_id);' style='width: 15%; text-align: center; font-size: 20px; color: #0000ff; cursor: pointer;'>EDIT</td>";
				else
					$table_data .= "<td style='width:15%; text-align:center; font-size: 20px;'>&nbsp;</td>";
                $table_data .= "<td onclick='deleteItemRow($res->item_id);' style='width: 15%; text-align: center; font-size: 20px; color: #0000ff; cursor: pointer;'>DEL</td></tr>";
                $total_amount = $total_amount + $cost;
            }
            else
            {
                $cost = $res->cost_price * $res->quantity;
                $table_data .= "<tr style='background-color:#ffffff;' onmouseover='' onmouseout=''>
                					<td style='width:30%; text-align:center; font-size: 20px;'>$res->name</td>
                					<td style='width:10%; text-align:right; font-size: 20px;'>$res->quantity</td>
                					<td style='width:10%; text-align:right; font-size: 20px;'>";
                $table_data .= to_currency($res->cost_price);
                $table_data .= "</td>
                				<td onclick='incQuantity(this);' style='width:10%; text-align:center; font-size: 20px; cursor: pointer;'>+</td>
                				<td onclick='decQuantity(this)' style='width:10%; text-align:center; font-size: 20px; cursor: pointer;'>-</td>";
                if($controller->Sale->is_item_kit($res->item_id))
                	$table_data .= "<td onclick='editItemRow($res->item_id);' style='width: 15%; text-align: center; font-size: 20px; color: #0000ff; cursor: pointer;'>EDIT</td>";
                else
                	$table_data .= "<td style='width:15%; text-align:center; font-size: 20px;'>&nbsp;</td>";

                $table_data .= "<td onclick='deleteItemRow($res->item_id);' style='width: 15%; text-align: center; font-size: 20px; color: #0000ff; cursor: pointer;'>DEL</td></tr>";
                $total_amount = $total_amount + $cost;
            }

            $nCount ++;
        }
    }
    else
        $table_data .= "<tbody><tr><td colspan='6' style='color: #aa3333; text-align: center; font-size: 20px;'>No items to display</td></tr></tbody>";
    $table_data .= "</table>,";
    $table_data .= $total_amount;

    return $table_data;

}


function get_phone_customer_manage_table($controller , $company_id)
{
	$CI =& get_instance();
	$table='<table style="width: 100%; text-align: center;" id="sortable_table">';

	$headers = array(
        '&nbsp;',
    	$CI->lang->line('common_name'),
    	$CI->lang->line('common_phone_number'),
        $CI->lang->line('common_address'),
        $CI->lang->line('common_zip')
	);

	$table.='<thead><tr>';
    $nCount = 0;
	foreach($headers as $header)
	{
	   if($nCount == 1 || $nCount == 3)
	       $table.="<th style='width:30%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 2)
           $table.="<th style='width:25%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 0)
           $table.="<th style='width:3%; background-color: #11ccdd;'>$header</th>";
       else
           $table.="<th style='width:15%; background-color: #11ccdd;'>$header</th>";
       $nCount ++;
	}
	$table.='</tr></thead><tbody>';
    $results = $controller->Phone->get_all_customer_info($company_id);
    $nCount = 0;
    foreach($results->result() as $res)
    {
        if($nCount % 2 == 0)
        {
            $table .= "<tr style='background-color:#cccccc;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#cccccc');\"><td><input type='checkbox' id='customer_$res->person_id' value='$res->person_id'/></td><td>$res->first_name";
            $table .= " ";
            $table .= "$res->last_name</td><td>$res->phone_number</td><td>$res->address_1</td><td>$res->zip</td></tr>";
        }
        else
        {
            if($company_id == 0)
                $table .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td><input type='checkbox' id='customer_$res->person_id' value='$res->person_id'/></td><td>$res->first_name";
            else
                $table .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td>$res->first_name";
            $table .= " ";
            $table .= "$res->last_name</td><td>$res->phone_number</td><td>$res->address_1</td><td>$res->zip</td></tr>";
        }
        $nCount ++;
    }
	$table.='</tbody></table>';
	return $table;
}



function get_phone_customer_manage_table_search($people , $controller)
{
	$CI =& get_instance();
	$table='<table style="width: 100%; text-align: center;" id="sortable_table">';

	$headers = array(
        '&nbsp;',
    	$CI->lang->line('common_name'),
    	$CI->lang->line('common_phone_number'),
        $CI->lang->line('common_address'),
        $CI->lang->line('common_zip')
	);

	$table.='<thead><tr>';
    $nCount = 0;
	foreach($headers as $header)
	{
	   if($nCount == 1 || $nCount == 3)
	       $table.="<th style='width:30%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 2)
           $table.="<th style='width:25%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 0)
           $table.="<th style='width:3%; background-color: #11ccdd;'>$header</th>";
       else
           $table.="<th style='width:15%; background-color: #11ccdd;'>$header</th>";
       $nCount ++;
	}
	$table.='</tr></thead><tbody>';
    //$results = $controller->Phone->get_all_customer_info();
    $nCount = 0;
    foreach($people->result() as $res)
    {
        if($nCount % 2 == 0)
        {
            $table .= "<tr style='background-color:#cccccc;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#cccccc');\"><td><input type='checkbox' id='customer_$res->person_id' value='$res->person_id'/></td><td>$res->first_name";
            $table .= " ";
            $table .= "$res->last_name</td><td>$res->phone_number</td><td>$res->address_1</td><td>$res->zip</td></tr>";
        }
        else
        {
            $table .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td><input type='checkbox' id='customer_$res->person_id' value='$res->person_id'/></td><td>$res->first_name";
            $table .= " ";
            $table .= "$res->last_name</td><td>$res->phone_number</td><td>$res->address_1</td><td>$res->zip</td></tr>";
        }
        $nCount ++;
    }
	$table.='</tbody></table>';
	return $table;
}



function get_delivery_list($controller , $controller_name)
{
	$CI =& get_instance();
    //$controller_name=strtolower(get_class($CI));
	$table='<table style="width: 100%; text-align: center;">';

	$headers = array(
    	$CI->lang->line('common_name'),
        $CI->lang->line('common_order'),
        '&nbsp'
	);

	foreach($headers as $header)
	{
	   if($nCount == 0)
	       $table.="<th style='width:40%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 1)
           $table.="<th style='width:40%; background-color: #11ccdd;'>$header</th>";
       else
           $table.="<th style='width:20%; background-color: #11ccdd;'>$header</th>";
       $nCount ++;
	}

    $table.='</tr></thead><tbody>';

    $results = $controller->Phone->get_all_delivery_info();
    $nCount = 0;
    foreach($results->result() as $res)
    {
        $customer_info = $controller->Customer->get_info($res->customer_id);
        if($res->sale_id == 0)
            $sale_order = "<td style='color:red;'>No Sale order to display. Plaese add Sale order.</td>";
        else
            $sale_order = "<td>Order #".$res->sale_id."</td>";
        if($nCount % 2 == 0)
        {
            $table .= "<tr style='background-color:#cccccc;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#cccccc');\"><td>$customer_info->first_name";
            $table .= " ";
            $table .= "$customer_info->last_name</td>";
            $table .= $sale_order;
            $table .= "<td>";
            $table .= anchor($controller_name."/edit_sale/$res->customer_id", $CI->lang->line('common_edit'),array('title'=>$CI->lang->line($controller_name.'_update')));
            $table .= "</td></tr>";
        }
        else
        {
            $table .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td>$customer_info->first_name";
            $table .= " ";
            $table .= "$customer_info->last_name</td>";
            $table .= $sale_order;
            $table .= "<td>";
            $table .= anchor($controller_name."/edit_sale/$res->customer_id", $CI->lang->line('common_edit'),array('title'=>$CI->lang->line($controller_name.'_update')));
            $table .= "</td></tr>";
//            $table .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td>$res->first_name";
//            $table .= " ";
//            $table .= "$res->last_name</td><td>$res->phone_number</td><td>$res->address_1</td><td>$res->zip</td><td>$res->delivery_time</td></tr>";
        }
        $nCount ++;
    }

	$table.='</tbody></table>';
	return $table;
}


function get_collect_list($controller , $controller_name)
{
	$CI =& get_instance();
    //$controller_name=strtolower(get_class($CI));
	$table='<table style="width: 100%; text-align: center;">';

	$headers = array(
    	$CI->lang->line('common_name'),
        $CI->lang->line('common_order'),
        '&nbsp'
	);

	foreach($headers as $header)
	{
	   if($nCount == 0)
	       $table.="<th style='width:40%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 1)
           $table.="<th style='width:40%; background-color: #11ccdd;'>$header</th>";
       else
           $table.="<th style='width:20%; background-color: #11ccdd;'>$header</th>";
       $nCount ++;
	}

    $table.='</tr></thead><tbody>';

    $results = $controller->Phone->get_all_collect_info();
    $nCount = 0;
    foreach($results->result() as $res)
    {
        $customer_info = $controller->Customer->get_info($res->customer_id);
        if($res->sale_id == 0)
            $sale_order = "<td style='color:red;'>No Sale order to display. Plaese add Sale order.</td>";
        else
            $sale_order = "<td>Order #".$res->sale_id."</td>";
        if($nCount % 2 == 0)
        {
            $table .= "<tr style='background-color:#cccccc;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#cccccc');\"><td>$customer_info->first_name";
            $table .= " ";
            $table .= "$customer_info->last_name</td>";
            $table .= $sale_order;
            $table .= "<td>";
            $table .= anchor($controller_name."/edit_collect_sale/$res->customer_id", $CI->lang->line('common_edit'),array('title'=>$CI->lang->line($controller_name.'_update')));
            $table .= "</td></tr>";
        }
        else
        {
            $table .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td>$customer_info->first_name";
            $table .= " ";
            $table .= "$customer_info->last_name</td>";
            $table .= $sale_order;
            $table .= "<td>";
            $table .= anchor($controller_name."/edit_collect_sale/$res->customer_id", $CI->lang->line('common_edit'),array('title'=>$CI->lang->line($controller_name.'_update')));
            $table .= "</td></tr>";
//            $table .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td>$res->first_name";
//            $table .= " ";
//            $table .= "$res->last_name</td><td>$res->phone_number</td><td>$res->address_1</td><td>$res->zip</td><td>$res->delivery_time</td></tr>";
        }
        $nCount ++;
    }

	$table.='</tbody></table>';
	return $table;
}


function get_edit_deliveries_table($controller , $group_id)
{
	$CI =& get_instance();
    $controller_name=strtolower(get_class($CI));


	$table='<table style="width: 100%; text-align: center;" id="sortable_table">';

	$headers = array(
        '&nbsp;',
    	$CI->lang->line('deliveries_address'),
    	$CI->lang->line('deliveries_contact_number'),
        $CI->lang->line('deliveries_post_code'),
        $CI->lang->line('deliveries_time')
	);

	$table.='<thead><tr>';
    $nCount = 0;
	foreach($headers as $header)
	{
	   if($nCount == 1)
	       $table.="<th style='width:30%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 2 || $nCount == 3)
           $table.="<th style='width:20%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 0)
           $table.="<th style='width:3%; background-color: #11ccdd;'>$header</th>";
       else
           $table.="<th style='width:27%; background-color: #11ccdd;'>$header</th>";
       $nCount ++;
	}
	$table.='</tr></thead><tbody>';

    $results = $controller->Delivery->get_deliveries_group_by_id($group_id);

    $nCount = 0;
    foreach($results->result() as $res)
    {
        if($nCount % 2 == 0)
            $table .= "<tr style='background-color:#cccccc;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#cccccc');\"><td><input type='checkbox' id='delivery_$res->delivery_id' value='$res->delivery_id' checked='true'/></td><td>$res->contact_address</td>";
        else
            $table .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td><input type='checkbox' id='delivery_$res->delivery_id' value='$res->delivery_id' checked='true'/></td><td>$res->contact_address</td>";

        $table .= "<td>$res->contact_number</td><td>$res->post_code</td><td>$res->delivery_time</td></tr>";
        $nCount ++;
    }

    $results = $controller->Delivery->get_all_ungroup_delivery_info();

    foreach($results->result() as $res)
    {
        if($nCount % 2 == 0)
            $table .= "<tr style='background-color:#cccccc;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#cccccc');\"><td><input type='checkbox' id='delivery_$res->delivery_id' value='$res->delivery_id'/></td><td>$res->contact_address</td>";
        else
            $table .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td><input type='checkbox' id='delivery_$res->delivery_id' value='$res->delivery_id'/></td><td>$res->contact_address</td>";

        $table .= "<td>$res->contact_number</td><td>$res->post_code</td><td>$res->delivery_time</td></tr>";
        $nCount ++;
    }

	$table.='</tbody></table>';

    $controller->Delivery->set_ungroup_deliveries($group_id);

	return $table;
}

function get_deliveries_table($controller)
{
	$CI =& get_instance();
    $controller_name=strtolower(get_class($CI));

	$table='<table style="width: 100%; text-align: center;" id="sortable_table">';

	$headers = array(
        '&nbsp;',
    	$CI->lang->line('deliveries_address'),
    	$CI->lang->line('deliveries_contact_number'),
        $CI->lang->line('deliveries_post_code'),
        $CI->lang->line('deliveries_time')
	);

	$table.='<thead><tr>';
    $nCount = 0;
	foreach($headers as $header)
	{
	   if($nCount == 1)
	       $table.="<th style='width:30%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 2 || $nCount == 3)
           $table.="<th style='width:20%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 0)
           $table.="<th style='width:3%; background-color: #11ccdd;'>$header</th>";
       else
           $table.="<th style='width:27%; background-color: #11ccdd;'>$header</th>";
       $nCount ++;
	}
	$table.='</tr></thead><tbody>';

    $results = $controller->Delivery->get_all_ungroup_delivery_info();

    $nCount = 0;
    foreach($results->result() as $res)
    {
        if($nCount % 2 == 0)
            $table .= "<tr style='background-color:#cccccc;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#cccccc');\"><td><input type='checkbox' id='delivery_$res->delivery_id' value='$res->delivery_id'/></td><td>$res->contact_address</td>";
        else
            $table .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td><input type='checkbox' id='delivery_$res->delivery_id' value='$res->delivery_id'/></td><td>$res->contact_address</td>";

        $table .= "<td>$res->contact_number</td><td>$res->post_code</td><td>$res->delivery_time</td></tr>";
        $nCount ++;
    }

	$table.='</tbody></table>';
	return $table;
}

function get_delivery_group_table($controller)
{
	$CI =& get_instance();
    $controller_name=strtolower(get_class($CI));

	$table='<table style="width: 100%; text-align: center;">';

	$headers = array(
    	$CI->lang->line('deliveries_group_number'),
    	$CI->lang->line('deliveries_group_created_time'),
        '&nbsp;',
        '&nbsp;',
        '&nbsp;'
	);

	$table.='<thead><tr>';
    $nCount = 0;
	foreach($headers as $header)
	{
	   if($nCount == 0 || $nCount == 1)
	       $table.="<th style='width:38%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 2 || $nCount == 3 || $nCount == 4)
           $table.="<th style='width:8%; background-color: #11ccdd;'>$header</th>";
       $nCount ++;
	}
	$table.='</tr></thead><tbody>';

    $results = $controller->Delivery->get_all_delivery_group_info();

    $nCount = 0;

    foreach($results->result() as $res)
    {
        if($nCount % 2 == 0)
            $table .= "<tr style='background-color:#cccccc;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#cccccc');\">";
        else
            $table .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\">";

        $table .= "<td>Group #$res->deliveries_group_id</td><td>$res->created_time</td><td onclick='click_group($res->deliveries_group_id , 1);'>";
        $table .= "<span style='color:blue; text-decoration: underline;'>edit</span>";
        $table .= "</td><td onclick='click_group($res->deliveries_group_id , 2);'>";
        $table .= "<span style='color:blue; text-decoration: underline;'>delete</span>";
        $table .= "</td><td onclick='click_group($res->deliveries_group_id , 3);'>";
        $table .= "<span style='color:blue; text-decoration: underline;'>print</span>";
        $table .= "</td></tr>";

        $nCount ++;
    }

    $table .= "</tbody></table>";
    return $table;
}


function get_delivery_receipt_table($controller , $group_id)
{
    //$table_data = "$group_id";
    $result_group = $controller->Delivery->get_group_by_id_row($group_id);
    $table_data = "<div id='receipt_group_info'><div id='group_id'>Deilvery Group #$result_group->deliveries_group_id</div><div id='created_time'>Created Time: $result_group->created_time</div></div><br /><br />";
	$results_orders = $controller->Delivery->get_deliveries_group_by_id($group_id);

    foreach($results_orders->result() as $res_rela_orders)
    {
        $table_data .= "<div><div>Contact Number : $res_rela_orders->contact_number</div><div>Contact Address : $res_rela_orders->contact_address</div><div>Post Code : $res_rela_orders->post_code</div><div>Delivery Time : $res_rela_orders->delivery_time</div></div>";
        $result_relation = $controller->Delivery->get_relation_delivery_orders($res_rela_orders->delivery_id);

        foreach($result_relation->result() as $res_relation)
        {
            $result_orders = $controller->Delivery->get_delivery_orders($res_relation->delivery_orders_id);

            foreach($result_orders->result() as $res_orders)
            {
                $customer_info = $controller->Customer->get_info($res_orders->customer_id);
                $table_data .= "<br><div>Customer : ";
                $table_data .= $customer_info->first_name;
                $table_data .= " ";
                $table_data .= $customer_info->last_name;
                $table_data .= "</div>";

                $table_data .= "<table id='receipt_items' style='width:100%;'><tr><th style='width:25%;text-align:center; border-bottom: 2px solid #000000;'>Item</th><th style='width:25%; text-align:right; border-bottom: 2px solid #000000;'>Price</th>";
                $table_data .= "<th style='width:25%;text-align:right; border-bottom: 2px solid #000000;'>Qty.</th><th style='width:25%;text-align:right; border-bottom: 2px solid #000000;'>Total</th></tr>";

                $sale_items_info = $controller->Sale->get_sale_items($res_orders->sale_id);

                $total_amount = 0;
                foreach($sale_items_info->result() as $res_sale_item)
                {
                    $res_item = $controller->Sale->get_item_name($res_sale_item->item_id);
                    $table_data .= "<tr><td style='text-align:center;'>$res_item->name</td><td style='text-align:right;'>";
                    $table_data .= to_currency($res_sale_item->item_cost_price);
    	            $table_data .= "</td><td style='text-align:right;'>";
                    $table_data .= round($res_sale_item->quantity_purchased);
                    $table_data .= "</td><td style='text-align:right;'>";
    		        $table_data .= to_currency($res_sale_item->item_cost_price * $res_sale_item->quantity_purchased);
                    $total_amount = $total_amount + $res_sale_item->item_cost_price * $res_sale_item->quantity_purchased;
                    $table_data .= "</td></tr><tr><td colspan='4' align='right'>";

            	    if($res_item->description == 0)
                        $table_data .= "&nbsp;";
                    else
                        $table_data .= $res_item->description;

                    $table_data .= "</td></tr>";
                }

                $table_data .= "<tr><td colspan='2' style='text-align:right;border-top:2px solid #000000;'>Total</td><td colspan='2' style='text-align:right;border-top:2px solid #000000;'>";
                $table_data .= to_currency($total_amount);
	            $table_data .= "</td></tr><tr><td colspan='6'>&nbsp;</td></tr></table><br />";
            }
        }

    }

    return $table_data;
}


function get_receive_payment_group_table($controller)
{
	$CI =& get_instance();
    $controller_name = strtolower(get_class($CI));
    $width = $controller->get_form_width();
	$table='<table style="width: 100%; text-align: center;" id="received_payment">';

	$headers = array(
    	$CI->lang->line('deliveries_group_number'),
    	$CI->lang->line('deliveries_group_created_time'),
        $CI->lang->line('deliveries_group_amount_due'),
        $CI->lang->line('deliveries_group_amount_received'),
        $CI->lang->line('deliveries_group_amount_comment'),
        '&nbsp;'
	);

	$table.='<thead><tr>';
    $nCount = 0;
	foreach($headers as $header)
	{
	   if($nCount == 0)
	       $table.="<th style='width:15%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 1)
           $table.="<th style='width:25%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 2)
           $table.="<th style='width:10%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 3)
           $table.="<th style='width:15%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 4)
           $table.="<th style='width:25%; background-color: #11ccdd;'>$header</th>";
       else
           $table.="<th style='width:10%; background-color: #11ccdd;'>$header</th>";
       $nCount ++;
	}
	$table.='</tr></thead><tbody>';

    $results = $controller->Delivery->get_all_delivery_group_info();

    $nCount = 0;

    foreach($results->result() as $res)
    {
        if($nCount % 2 == 0)
            $table .= "<tr style='background-color:#cccccc;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#cccccc');\">";
        else
            $table .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\">";

        $table .= "<td>Group #$res->deliveries_group_id</td><td>$res->created_time</td><td style='text-align:right'>$res->amount_due</td><td style='text-align:right'>$res->amount_received</td><td>$res->amount_comment</td>";
        $table .= "<td>";
        $table .= anchor($controller_name."/edit_group/$res->deliveries_group_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update')));
        $table .= "</td></tr>";

        $nCount ++;
    }

    $table .= "</tbody></table>";
    return $table;
}


function get_order_manage_table($limit , $offset , $controller , $view_category)
{
    $CI =& get_instance();
    $controller_name = strtolower(get_class($CI));

    $table_data = "<table style='width: 100%; text-align: center;'>";
   	$headers = array(
    	"Customer Name",
    	"Created Time",
        "Total Amount",
        '&nbsp;'
	);
	$table.='<thead><tr>';
    $nCount = 0;
	foreach($headers as $header)
	{
	   if($nCount == 0)
	       $table_data .= "<th style='width:30%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 1)
           $table_data .= "<th style='width:25%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 2)
           $table_data .= "<th style='width:25%; background-color: #11ccdd;'>$header</th>";
       else if($nCount == 3)
           $table_data .= "<th style='width:20%; background-color: #11ccdd;'>$header</th>";
       $nCount ++;
	}
	$table_data .= '</tr></thead><tbody>';
    if($view_category == 1) //Delivery Order
    {

        $result_delivery_group = $controller->ViewOrder->get_all_delivery_group();
        $nCount = 0;
        foreach($result_delivery_group->result() as $res_delivery_group)
        {
            $result_delivery = $controller->ViewOrder->get_delivery($res_delivery_group->deliveries_group_id);

            foreach($result_delivery->result() as $res_delivery)
            {
                $result_delivery_orders = $controller->ViewOrder->get_delivery_orders($res_delivery->delivery_id);

                foreach($result_delivery_orders->result() as $res_delivery_orders)
                {
                    $total_amount = 0;
                    $customer_info = $controller->Customer->get_info($res_delivery_orders->customer_id);
                    $result_sales_items = $controller->ViewOrder->get_sale_total_amount($res_delivery_orders->sale_id);
                    $customer_name = $customer_info->first_name." ".$customer_info->last_name;
                    foreach($result_sales_items->result() as $res_sales_items)
                    {
                        $total_amount = $total_amount + $res_sales_items->item_cost_price * $res_sales_items->quantity_purchased;
                    }
                    if($nCount % 2 == 0)
                        $table_data .= "<tr style='background-color:#cccccc;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#cccccc');\"><td>$customer_name</td><td>$res_delivery->delivery_time</td><td>$total_amount</td><td>";
                    else
                        $table_data .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td>$customer_name</td><td>$res_delivery->delivery_time</td><td>$total_amount</td><td>";

                    if($res_delivery_group->completed == 1)
                        $table_data .= "completed";
                    else
                        $table_data .= "uncompleted";

                    $table_data .= "</td></tr>";
                    $nCount ++;
                }

            }

        }
    }

    if($view_category == 2) //Collect Order
    {
        $result_collect = $controller->ViewOrder->get_collect();

        foreach($result_collect->result() as $res_collect)
        {
            $result_collect_orders = $controller->ViewOrder->get_collect_orders($res_collect->collect_id);

            foreach($result_collect_orders->result() as $res_collect_orders)
            {
                $total_amount = 0;
                $customer_info = $controller->Customer->get_info($res_collect_orders->customer_id);
                $result_sales_items = $controller->ViewOrder->get_sale_total_amount($res_collect_orders->sale_id);
                $customer_name = $customer_info->first_name." ".$customer_info->last_name;
                foreach($result_sales_items->result() as $res_sales_items)
                {
                    $total_amount = $total_amount + $res_sales_items->item_cost_price * $res_sales_items->quantity_purchased;
                }
                if($nCount % 2 == 0)
                    $table_data .= "<tr style='background-color:#cccccc;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#cccccc');\"><td>$customer_name</td><td>$res_collect->collect_time</td><td>$total_amount</td><td>";
                else
                    $table_data .= "<tr style='background-color:#ffffff;' onmouseover=\"mouse_over(this , '#11d211');\" onmouseout=\"mouse_out(this , '#ffffff');\"><td>$customer_name</td><td>$res_collect->collect_time</td><td>$total_amount</td><td>";

                $table_data .= "&nbsp;";

                $table_data .= "</td></tr>";
                $nCount ++;
            }
        }
    }

    if($view_category == 3)
    {

    }

    $table_data .= "</table>";
    return $table_data;
}

?>