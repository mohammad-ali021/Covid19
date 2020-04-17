<?php

if(! function_exists('putHeader'))
{
	function putHeader()
	{
		$ci=&get_instance();
                $ci->lang->load('home',$ci->m_auth->get_language_name());
		return $ci->load->view('template/header');
	}
}

if(! function_exists('putTop'))
{
	function putTop()
	{
                $CI=&get_instance();
		
		return $CI->load->view('template/top');
	}
}
if(! function_exists('putLeft'))
{
	function putLeft()
	{
                $CI=&get_instance();
                
                return $CI->load->view('template/left');
                
	}
}

if(! function_exists('putContent'))
{
	function putContent($contents)
	{
		$ci=&get_instance();
		$data['contents']=$contents;
		return $ci->load->view('template/content',$data);
	}
}

if(! function_exists('putFooter'))
{
	function putFooter()
	{
		$ci=&get_instance();
                //return "";
		return $ci->load->view('template/footer');
	}
}

// Genral dropdown for stafic values By @Ali Abassi 
if(!function_exists('genDropdown'))
{
    function genDropdown($table,$selected_item=0,$colum='id',$whr='',$name='name_en',$groupby='')
    {
        $ci = &get_instance();
        $ci->load->model('frontend_model');
        $data = $ci->frontend_model->getData($table,$whr,$groupby);
        $option="";
         if($data)
         {
            foreach($data->result() as $row)
            {
                $option.= '<option '.($row->$colum == $selected_item ? "selected":"").' value="'.$row->$colum.'">'.$row->$name.'</option>';
            }
         }        
         return $option;
    }
}
// function to get the static name form table 
if(!function_exists('getName'))
{
    function getName($table,$value='*',$selector='',$whr='')
    {
        $ci = &get_instance();
        $ci->load->model('frontend_model');
        $data = $ci->frontend_model->getName($table,$value,$selector,$whr);
        $item=$data->row();
        return $item->$value;
    }
}

// Months dropdown for stafic values By @Ali Abassi 
if(!function_exists('monthDropdown'))
{
    function monthDropdown($selected)
    {
        $ci = &get_instance();
        
        $option='<option '.($selected==""? 'selected="selected"':'').' value="">-- Please Select --</option>
                 <option '.($selected==1? 'selected="selected"':'').' value="1">January</option>
                 <option '.($selected==2? 'selected="selected"':'').' value="2">February</option>
                 <option '.($selected==3? 'selected="selected"':'').' value="3">March</option>
                 <option '.($selected==4? 'selected="selected"':'').' value="4">April</option>
                 <option '.($selected==5? 'selected="selected"':'').' value="5">May</option>
                 <option '.($selected==6? 'selected="selected"':'').' value="6">June</option>
                 <option '.($selected==7? 'selected="selected"':'').' value="7">July</option>
                 <option '.($selected==8? 'selected="selected"':'').' value="8">August</option>
                 <option '.($selected==9? 'selected="selected"':'').' value="9">September</option>
                 <option '.($selected==10? 'selected="selected"':'').' value="10">October</option>
                 <option '.($selected==11? 'selected="selected"':'').' value="11">November</option>
                 <option '.($selected==12? 'selected="selected"':'').' value="12">December</option>';        
         return $option;
    }
}

 //return rtl or fload for language change
 if(!function_exists('get_dir'))
  {
    function get_dir($arg='dir')
    {
       $CI =& get_instance();
       if($CI->m_auth->get_language()=='en'){
           if($arg!='dir')
           {  
             return "left";
           }else {
             return "ltr";
           }
       }else{
          if($arg!='dir')
           {  
              return "right";
           }else {
              return "rtl";
           } 
       }
       
    }
  }
  
  // Genral dropdown for stafic values By @Ali Abassi 
if(!function_exists('boaleanDropdown'))
{
    function boaleanDropdown($selected_item='',$text=false)
    {
        $ci = &get_instance();
        $option="";
        if($text==false){

          $option.= '<option '.($selected_item == '' ? "selected":"").' value="">-- Please Select --</option>';
          $option.= '<option '.($selected_item == 1 ? "selected":"").' value="1">Yes</option>';
          $option.= '<option '.($selected_item == 2 ? "selected":"").' value="2">NO</option>';
        }else {
            if ($selected_item==1){
                $option="Yes";
            }else if ($selected_item==2){
                $option="NO";
            }else{
                $option="-";
            }
        }
        return $option;
    }
}

?>