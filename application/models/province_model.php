<?php
	/*
	* Province which provides INSERT, UPDATE, SELECT and LOG action to all requests for province model
	* version: 1.0
	* create date: 29/10/2016
	* Author: Ali Abassi
	*/
  
class Province_model extends CI_Model
{
    
    //private $db;
    //private $prefix = "aeip";
	//constructor function
	function __construct()
	{
		parent:: __construct();
		//$this->db = $this->load->database('aeip',TRUE);
	}
	//class destructors function
	function __destruct()
	{
		//do nothing
	}
	//count toal record from a table
	function TotalRecords()
	{    
		$this->db->select('code');
		$this->db->from('provinces');
		
		//order the records
		$this->db->group_by("code"); 
		$q = $this->db->get();
		//echo $this->hr_db->last_query();exit;
		if($q)
		{
			 return  $q->num_rows();
		}
		else
		{
		   return 0;
		}
	} 
	
	function GetAllProvinces($offset, $nrecords, $istotal=FALSE,$lang='en')
	{
		// GetDetails function gets all information of province table
		//get one table data
		if($lang == 'en')
		{
		   $province = "TRIM(name_en)";
		}
		else if($lang == 'da')
		{
		   $province = "TRIM(name_da)";
		}
		else
		{
		   $province = "TRIM(name_da)";     
		}


		$this->db->select('id,'.$province.' AS name,name_da AS province_da,name_en AS province_en,code,zone');
		$this->db->from($this->prefix.'_province');
		
		//order the records
		$this->db->order_by($province,"ASC");
		$this->db->group_by("code"); 
		//if there is a limitation data retrieve
		if(!$istotal)
		{
		   $this->db->limit($nrecords,$offset);
		}
		
		$query=$this->db->get();
		echo($this->db->last_query());exit;
		if ($query->num_rows()>0)
		{
		   // returns the data which is stored in $query
			return $query;
		}
		else
		{
			return FALSE;
		}
	}
	
	
	//get one record details from relathionship table
	function GetOneRecord($table="",$fields="*",$field="",$id=0)
	{
        
	   //if specified fileds are empty than send 0 (zero)
	   if(!empty($table) && !empty($fields) && !empty($field) && $id != 0)
	   {
          $this->db->select($fields);
		  $this->db->from($table);
		  $this->db->where($field,$id);
		  $q_result = $this->db->get();
		  //check if there is any record comming from db
		  if($q_result->num_rows()>0)
		  {
				//retrun $q_result object11
				return $q_result;
		  }
		  else
		  {
			  return  0;
		  }
	   
	   }
	   else
	   {
		  return  0;
	   }
	}  
	
	
	 //get list of district by province code
	function GetDistrictsByProvince($offset, $nrecords, $istotal=FALSE,$lang='en',$provincecode=null)
	{                                                         
		// GetDetails function gets all information of bankname table
		//get one table data
		
		if($lang == 'en')
		{
		   $district = "district_en";
		   $province = "province_en";
		}
		else if($lang == 'da')
		{
		   $district = "district_da";
		   $province = "province_da";
		}
		else
		{
		   $district = "district_da";
		   $province = "province_da";     
		}
		$this->db->select('id,'.$district.' AS name,districtcode AS code, provincecode As prcode, district_en AS district_en, district_da AS district_da, zone As zone, '.$province.' AS province');
		$this->db->from('districts');
		if (strpos($provincecode, '-1') !== false) {
				$provincecode='0';
			}
		//set where clause of province code
		$whr='provincecode ="'.$provincecode.'"';
		$this->db->where($whr);
		//order the records
		//$this->db->order_by('name',"ASC");
		$this->db->group_by("code"); 
		//if there is a limitation data retrieve
		if(!$istotal)
		{
		   $this->db->limit($nrecords,$offset);
		}
		
		$query=$this->db->get();
	   // echo $this->db->last_query();
		if ($query->num_rows()>0)
		{
		   // returns the data which is stored in $query
			return $query;
		}
		else
		{
			return FALSE;
		}
	}
	
	
	//get list of district by province code
	function GetVillageByDistrict($offset, $nrecords, $istotal=FALSE,$lang='en',$districtcode=null)
	{
		// GetDetails function gets all information of bankname table
		//get one table data
		if($lang == 'en')
		{
		   $village = "village_en";
		   $province= "province_en";
		   $district="district_en";
		}
		else if($lang == 'da')
		{
			$village = "village_da";
			$province= "province_da";
			$district="district_da"; 
		}
		else
		{
		   $village = "village_da";
		   $province= "province_da";
		   $district="district_da";     
		}
		$this->db->select('id,'.$village.' AS villagename,'.$district.' AS name, '.$province.' As province, village_en As village_en, village_da, villagecode, zone');
		$this->db->from('districts');
		if (strpos($districtcode, '-1') !== false) {
				$districtcode='0';
			}
		//set where clause of province code
		$whr=" districtcode IN (".$districtcode.") ";
		$this->db->where($whr);
		//order the records
		$this->db->order_by($village,"ASC");
		$this->db->group_by("villagecode"); 
		//if there is a limitation data retrieve
		if(!$istotal)
		{
		   $this->db->limit($nrecords,$offset);
		}
		
		$query=$this->db->get();
		//echo $this->hr_db->last_query();
		if ($query->num_rows()>0)
		{
		   // returns the data which is stored in $query
			return $query;
		}
		else
		{
			return FALSE;
		}
	}
	    
        
    function GetFacilityByProvince($offset, $nrecords, $istotal=FALSE,$lang='en',$provincecode=null)
    {                                                         
        // GetDetails function gets all information of bankname table
        //get one table data
        $this->db->select('id as code,name_en AS name');
        $this->db->from('facility');
        //set where clause of province code
        $whr='provincecode ="'.$provincecode.'"';
        $this->db->where($whr);
        //order the records
        $this->db->order_by('name_en',"ASC");
        //if there is a limitation data retrieve
        if(!$istotal)
        {
           $this->db->limit($nrecords,$offset);
        }
        
        $query=$this->db->get();
       // echo $this->db->last_query();
        if ($query->num_rows()>0)
        {
           // returns the data which is stored in $query
            return $query;
        }
        else
        {
            return FALSE;
        }
    }
    
    
    function GetProvinceByRegion($offset, $nrecords, $istotal=FALSE,$lang='en',$provincecode=null)
    {                                                         
        // GetDetails function gets all information of bankname table
        //get one table data
        $this->db->select('code as code,name_en AS name');
        $this->db->from('provinces');
        //set where clause of province code
        $whr='region_code ="'.$provincecode.'"';
        $this->db->where($whr);
        //order the records
        $this->db->order_by('name_en',"ASC");
        //if there is a limitation data retrieve
        if(!$istotal)
        {
           $this->db->limit($nrecords,$offset);
        }
        
        $query=$this->db->get();
       // echo $this->db->last_query();
        if ($query->num_rows()>0)
        {
           // returns the data which is stored in $query
            return $query;
        }
        else
        {
            return FALSE;
        }
    }

}
// end of medel