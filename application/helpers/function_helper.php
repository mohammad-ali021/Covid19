<?php

/**
 * Get data from model and create dropdown
 * 
 * @access public
 * @param $table string
 * @param $lang string
 * @param $value string
 * @param $name string
 * @return string
 */

//get all Stock Item Names.
if(!function_exists('getYearItem'))
{
    function getYearItem($selected_item=0)
    {
        $ci = &get_instance();
        //$ci->load->model('stock_in_model');
        $items = 2000;
        //print_r($future_events);
        $options = "<option value=''>Start Year </option>";
        //foreach the provinces
        if($items)
        { 
            for($items=2000;$items <= 2050;$items +=1)
            {   
                $options .= '<option '.($items == $selected_item ? "selected":"").' value="'.$items.'">'.$items.'</option>';
            }
            return $options;
        }
        else
        {
            echo "<center><div class='alert alert-danger'>There is no Item Name</div></center>";
        }
        
    }
    
}
//get kitchen's good type.
if(!function_exists('getGoodsType'))
{
    function getGoodsType($selected_item=0)
    {
        $ci = &get_instance();
        $ci->load->model('kitchen_goods/goods_model');
        $items = $ci->goods_model->getGoodsType();
        //print_r($future_events);
        $options = "<option value=''> Select good type </option>";
        //foreach the provinces
        if($items)
        { 
            foreach($items AS $item)
            {   
                $options .= '<option '.($item->id == $selected_item ? "selected":"").' value="'.$item->id.'">'.$item->name.'</option>';
            }
            return $options;
        }
        else
        {
            echo "<center><div class='alert alert-danger'>There is no record inserted in the system.</div></center>";
        }
        
    }
    
}

//get kitchen's good type.
if(!function_exists('getGoodTypeName'))
{
    function getGoodTypeName($good_type_id)
    {
        $ci = &get_instance();
        $ci->load->model('kitchen_goods/goods_model');
        $row = $ci->goods_model->getGoodsName($good_type_id);
        $name = $row->name;
        return $name;
    }
    
}

//get the total of quantity based on item_id.
if(!function_exists('getTotalOfQuantity'))
{
    function getTotalOfQuantity($item_id, $table_name)
    {
        $ci = &get_instance();
        $total_quantity = $ci->db->select_sum('quantity')->from($table_name)->where('item_name', $item_id)->get()->row();
        return $total_quantity->quantity;
    }
    
}


