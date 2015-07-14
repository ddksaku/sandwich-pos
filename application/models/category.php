<?php
class Category extends CI_Model
{
	/*
	Determines if a given item_id is an item
	*/
	function exists($category_id)
	{
		$this->db->from('categories');
		$this->db->where('category_id',$category_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}    
    
	/*
	Returns all the items
	*/
	function get_all($limit=10000, $offset=0)
	{
		$this->db->from('categories');
		$this->db->where('deleted',0);
		$this->db->order_by("category_id", "asc");
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}
    
    function get_categories($limit=10000 , $offset=0)
    {
        $this->db->from('categories');
        $this->db->where("(parent_id)='0' and (deleted)='0'");
        //$this->db->where('deleted',0);
        $this->db->order_by('category_id' , 'asc');
        $this->db->limit($limit);
 		$this->db->offset($offset);
		return $this->db->get();
    }
    
    function get_sub_categories($parent_category_id)
    {
        $this->db->from('categories');
        $this->db->where("(deleted)='0' and (parent_id)='".$parent_category_id."'");
        //$this->db->where('deleted',0);
        $this->db->order_by('category_id' , 'asc');
		return $this->db->get();        
    }


	function search($search)
	{
		$this->db->from('categories');
		$this->db->where("(name LIKE '%".$this->db->escape_like_str($search)."%') and (deleted)='0' and (parent_id)='0'");
		$this->db->order_by("category_id", "asc");
		return $this->db->get();	
	}
    
        
	function count_all()
	{
		$this->db->from('categories');
		$this->db->where('deleted',0);
		return $this->db->count_all_results();
	}
    
    
	/*
	Gets information about a particular item
	*/
	function get_info($category_id)
	{
		$this->db->from('categories');
		$this->db->where('category_id',$category_id);
		
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $category_id is NOT an category
			$category_obj=new stdClass();

			//Get all the fields from categories table
			$fields = $this->db->list_fields('categories');

			foreach ($fields as $field)
			{
				$category_obj->$field='';
			}

			return $category_obj;
		}
	}    
    
    
    /*
	Inserts or updates a item
	*/
	function save(&$category_data,$category_id=false)
	{
		if (!$category_id or !$this->exists($category_id))
		{
			if($this->db->insert('categories',$category_data))
			{
				$category_data['category_id']=$this->db->insert_id();
				return true;
			}
			return false;
		}
        
        $result = $this->db->query("select * from ospos_categories where category_id='".$category_id."'")->row();
        
        $category_name = $result->name;
        $replace_name = $category_data['name'];
		$this->db->where('category_id', $category_id);
		$this->db->update('categories',$category_data);
        
        $temp_data = array('category'=>$replace_name);
        $this->db->where('category' , $category_name);
        $this->db->update('items' , $temp_data);
        return true;
        
        
	}



 	/*
	Get search suggestions to find items
	*/
	function get_search_suggestions($search,$limit=25)
	{
		$suggestions = array();

		$this->db->from('categories');
		$this->db->like('name', $search);
		$this->db->where('deleted',0);
		$this->db->order_by("name", "asc");
		$by_name = $this->db->get();
		foreach($by_name->result() as $row)
		{
			$suggestions[]=$row->name;
		}

		$this->db->select('category');
		$this->db->from('items');
		$this->db->where('deleted',0);
		$this->db->distinct();
		$this->db->like('category', $search);
		$this->db->order_by("category", "asc");
		$by_category = $this->db->get();
		foreach($by_category->result() as $row)
		{
			$suggestions[]=$row->category;
		}

		$this->db->from('items');
		$this->db->like('item_number', $search);
		$this->db->where('deleted',0);
		$this->db->order_by("item_number", "asc");
		$by_item_number = $this->db->get();
		foreach($by_item_number->result() as $row)
		{
			$suggestions[]=$row->item_number;
		}


		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;

	}
    
    
	/*
	Deletes one item
	*/
	function delete($category_id)
	{
		$this->db->where('category_id', $category_id);
		return $this->db->update('categories', array('deleted' => 1));
	}

	/*
	Deletes a list of items
	*/
	function delete_list($category_ids)
	{
        for($nCount = 0 ; $nCount < sizeof($category_ids) ; $nCount ++)
        {
            $result = $this->db->query("select * from ospos_categories where category_id='".$category_ids[$nCount]."'")->row();
            if($result->parent_id == 0)
            {
                $result_sub_category = $this->db->query("select * from ospos_categories where parent_id='".$result->category_id."'");
                foreach($result_sub_category->result() as $res_sub_category)
                {
                    $this->db->query("delete from ospos_items where category='".$res_sub_category->name()."'");
                }
            }
            else
                $this->db->query("delete from ospos_items where category='".$result->name."'");
            $this->db->query("delete from ospos_categories where category_id='".$category_ids[$nCount]."'");
            $this->db->query("delete from ospos_categories where parent_id='".$category_ids[$nCount]."'");
                 
        }
        return true;
		//$this->db->where_in('category_id',$category_ids);
		//return $this->db->delete('categories');
        //return $this->db->update('categories', array('deleted' => 1));
 	}    
}
?>
