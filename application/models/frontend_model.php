<?php
  class Frontend_model extends CI_Model{
                                                 
      //---Function to get Records
      function getRecords($table="",$whr="",$limit="",$order="")
      {                 
        if($table!=""){ 
            $this->db->select("*");
            $this->db->from($table);           
            if($whr!=""){
                $this->db->where($whr);
            }if($limit!=""){ 
                $limitArray = explode(',', $limit);
                if(count($limitArray)>1){
                    $start  =$limitArray[1];
                    $end=$limitArray[0];
                    $this->db->limit($start,$end);
                }else{
                $this->db->limit($limit); 
                }    
            }               
            if($order!="")
            {
                $this->db->order_by($order);
            }
            $result = $this->db->get();
            //echo($this->db->last_query());
            if($result && $result->num_rows() > 0){
                return $result;
            }else{
                return false;
            }
        }
      } 
      
      function addRecords($table="",$data=array())
      {
        $this->db->insert($table,$data);
        if($this->db->affected_rows() > 0)
        {
            return true;
        }else{
            return false;
        }
      } 
      //---add Records and Return back ID
      function addRecordsGetId($table="",$data=array())
      {
            $this->db->trans_start();    
            $this->db->insert($table,$data);
            $id = $this->db->insert_id();
            $this->db->trans_complete();
            return $id;
      } 
      
      
      function InsertBatch($table="",$data=array())
      {           
        if(is_array($data))
        {   
            $this->db->trans_start();  
            $this->db->insert_batch($table,$data);
            $this->db->trans_complete();
            return true;
        }
      }   
      
      
      
      function getPage()
      {
        $this->db->select("t1.*,t2.name as cat_name");
        $this->db->from('page as t1');
        $this->db->join('category as t2','t1.category = t2.id');
        $result = $this->db->get();
        //echo $this->db->last_query(); exit;
        if($result && $result->num_rows() > 0){
            return $result;
        }else{
            return false;
        }
      }      
      
      //--Edit Records
      function editRecords($table="",$data=array(),$whr="")
      {         
          if($table!="" and is_array($data))
          {    
            $this->db->trans_start();
            $this->db->where($whr);
            $this->db->update($table,$data);
            $this->db->trans_complete();
            return true;
          }
      }
      
      //--Delete
      function deleteRecords($tbname="",$where="")
      {
        if($tbname!="")
        {    
            $this->db->trans_start();
            $this->db->where($where);
            $this->db->delete($tbname);
            $this->db->trans_complete();   
            return true;
            
         }
      }
      
      function getAll($table,$category=0,$offset=0,$per=0,$total=FALSE,$type='',$title='',$name='',$job_category_id='',$organization_id='',$category_id='',$province_id='', $id='')
      {
        // ex($name);
            $this->db->select("*");
            $this->db->from($table);
            // if($type == '')
            // {
            //   $this->db->where('type',$type);
            // }
            if($title != '')
            {
              $this->db->where('title_en',$title);
            }
            if($province_id !='')
            {
              $this->db->where('province',$province_id);
            }
             if($job_category_id !='')
            {
              $this->db->where('category_id',$job_category_id);
            }
             if($name !='')
            {
              $this->db->like('title_en',$name);
            }
             if($organization_id !='')
            {
              $this->db->where('organization_id',$organization_id);
            }
             if($category_id !='')
            {
              $this->db->where('category_id',$category_id);
            }

            if($category != 0){
                $this->db->where('category = '.$category.'');
            }
            if($id != 0){
                $this->db->where('category_id = '.$id.'');
            }
            if($table == 'library'){
              $this->db->where('classification=1');
            }
            if($table == 'projects' || $table == 'jobs'|| $table == 'tenders'){
              $this->db->where('classified=1');
            }
            if($this->input->post('org_name_serach') != ''){
              $this->db->like('org_name', $this->input->post('org_name_serach'));
            }
            $order = "id desc";
            $this->db->order_by($order);
            /*if($total!=TRUE)
            {
                $this->db->limit($per,$offset);
            }*/
            $object = $this->db->get();
            //echo($this->db->last_query());exit;
            //print_r($object->result());exit;
            if($object && $object->num_rows()>0)
            {
                if($total==TRUE)
                {
                    return $object->num_rows();
                }
                else
                {
                    return $object;
                }
            }
            else
            {
                return false;
            }
            
        }
		
		function getData($table,$whr='',$groupby=''){
			$this->db->select("*");
            $this->db->from($table);
			if($whr!=''){
            	$this->db->where($whr);
			}
            if($groupby!=''){
                $this->db->group_by($groupby);
            }
            $object = $this->db->get();
            //echo ($this->db->last_query());// exit;
            if($object && $object->num_rows()>0)
            {
            	return $object;
            }
            else
            {
            	return false;
            }
			
		}
		
		//---Function to get Records
      function getProvinceCode($prName)
      {                 
        $this->db->select('code');
        $this->db->from('provinces');           
        $this->db->where('name_'.$this->m_lang->get_language().' = "'.$prName.'"');
        $result = $this->db->get();
        //echo($this->db->last_query());exit;
        if($result && $result->num_rows() > 0){
            return $result;
        }else{
            return false;
        }
      }
	  
	  // get project by fund and status for Energy sector 
	  function get_status_by_province($prCode=0,$lang='en',$project_type=0,$energy=0,$electricity_or_mins=false){
		  
		  $project_sql="";
		  if ($project_type!=0){
			  if($energy!=0){
				  $project_sql=" AND subenergy_type_id=".$energy;
			  }else {
			  	  $project_sql=" AND Projects_type=".$project_type;
			  }
		  }else if($energy!=0 && $project_type==0 ){
		  		  $project_sql=" AND subenergy_type_id=".$energy;
		  }
		  if($prCode!=0){
			  $query="SELECT 
				name_".$lang." AS `status`,
				(SELECT COUNT(*) FROM projects WHERE projects.status=status.id AND province_id='".$prCode."' ".$project_sql.") AS 'total'
				FROM status
				ORDER BY name_".$lang." ASC";
		  }else{
			   $query="SELECT 
				name_".$lang." AS `status`,
				(SELECT COUNT(*) FROM projects WHERE projects.status=status.id ".$project_sql.") AS 'total'
				FROM status
				ORDER BY name_".$lang." ASC";
		  }
			$result=$this->db->query($query);
			//echo ($this->db->last_query());exit;
			if ($result){
				return $result;
			}else{
				return false;
			}
	  }
	  
	  // get Energy Information by province and information type for Energy sector 
	  function get_information_by_province($prCode=0,$lang='en',$energyType=0){
		  $energy_sql="";
		  if ($energyType!=0){
			  $energy_sql=" AND subenergy_type_id=".$energyType;;
		  }
		  if($prCode!=0){
			  $query="SELECT 
				name_".$lang." AS `type`,
				(SELECT SUM(number) FROM `energy` WHERE `energy`.`information_type_id`=`energy_information_types`.id  AND `energy`.`province_id`= '".$prCode."'".$energy_sql.") AS 'total'
				FROM `energy_information_types`
				ORDER BY name_".$lang." ASC ";
		  }else{
			   $query="SELECT 
				name_".$lang." AS `type`,
				(SELECT SUM(number) FROM `energy` WHERE `energy`.`information_type_id`=`energy_information_types`.id ) AS 'total'
				FROM `energy_information_types`
				ORDER BY name_".$lang." ASC ";
		  }
		  
			$result=$this->db->query($query);
			// echo ($this->db->last_query());exit;
			if ($result){
				return $result;
			}else{
				return false;
			}
	  }
	  
	  
	  // get Energy Information by province and information type by year for Energy sector 
	  function get_information_by_province_year($prCode=0,$lang='en'){
		  if($prCode!=0){
		  $query="SELECT en.year, 
		          (SELECT IFNULL(SUM(number),0) FROM `energy` WHERE energy.`information_type_id` = 1 AND energy.year=en.year AND energy.`province_id`='".$prCode."' )AS type1,
		          (SELECT IFNULL(SUM(number),0) FROM `energy` WHERE energy.`information_type_id` = 2 AND energy.year=en.year AND energy.`province_id`='".$prCode."' )AS `type2`,
		          (SELECT IFNULL(SUM(number),0) FROM `energy` WHERE energy.`information_type_id` = 3 AND energy.year=en.year AND energy.`province_id`='".$prCode."' )AS type3,
				  (SELECT IFNULL(SUM(number),0) FROM `energy` WHERE energy.`information_type_id` = 4 AND energy.year=en.year AND energy.`province_id`='".$prCode."' )AS type4,
				  (SELECT IFNULL(SUM(number),0) FROM `energy` WHERE energy.`information_type_id` = 5 AND energy.year=en.year AND energy.`province_id`='".$prCode."' )AS type5
		          FROM energy en
		          GROUP BY en.year
		          ORDER BY en.year ASC ";
		  /*$energyinformationType=$this->getRecords('energy_information_types');
		  if ($energyinformationType){
			  foreach($energyinformationType->result() as $item){
				  $query.="(SELECT IFNULL(SUM(number),0) FROM `energy` WHERE energy.`information_type_id` = ".$item->id." AND energy.year=en.year AND energy.`province_id`='".$prCode."')AS '".getName('energy_information_types',$item->id,'name_en','id',1)."',";
			  }
		  }
          $queryEnd="FROM energy en 
          GROUP BY en.year
          ORDER BY en.year ASC ";
		  $query=trim($query,',');
		  $query.=$queryEnd;*/
		  }else{
			   $query="SELECT en.year, 
		          (SELECT IFNULL(SUM(number),0) FROM `energy` WHERE energy.`information_type_id` = 1 AND energy.year=en.year )AS type1,
		          (SELECT IFNULL(SUM(number),0) FROM `energy` WHERE energy.`information_type_id` = 2 AND energy.year=en.year )AS `type2`,
		          (SELECT IFNULL(SUM(number),0) FROM `energy` WHERE energy.`information_type_id` = 3 AND energy.year=en.year )AS type3,
				  (SELECT IFNULL(SUM(number),0) FROM `energy` WHERE energy.`information_type_id` = 4 AND energy.year=en.year )AS type4,
				  (SELECT IFNULL(SUM(number),0) FROM `energy` WHERE energy.`information_type_id` = 5 AND energy.year=en.year )AS type5
		          FROM energy en
		          GROUP BY en.year
		          ORDER BY en.year ASC ";
		  }
		  
			$result=$this->db->query($query);
			//echo ($this->db->last_query());exit;
			if ($result){
				return $result;
			}else{
				return false;
			}
	  }
	  
      function get_organization_name($id)
      {
        $this->db->select('*')->from('organization');
        $this->db->where('id',$id);
        return $this->db->get()->row();
      }
      function get_cities()
      {
        $this->db->select('*')->from('city');
        $this->db->where('CountryCode','AFG');
        return $this->db->get()->result();
      }
      function get_job_categories()
      {
        $this->db->select('*')->from('job_category');
        return $this->db->get()->result();
      }
      function get_job_detail($id)
      {
        $this->db->select('organization.description_'.$this->m_lang->get_language().' as odescription,jobs.description_'.$this->m_lang->get_language().' as jdescription, title_'.$this->m_lang->get_language().' as title, organization.name_'.$this->m_lang->get_language().' as organname,announced_date,closing_date,vac,no_jobs,gender,nationality_'.$this->m_lang->get_language().' as nationality,year_experiance,contract_duration_'.$this->m_lang->get_language().' as contract_duration,job_requirements_'.$this->m_lang->get_language().' as job_requirements,submission_guideline_'.$this->m_lang->get_language().' as submission_guideline,submission_email ' )->from('jobs');
        $this->db->join('organization','organization.id=jobs.organization_id','left');
        $this->db->where('jobs.id',$id);
        return $this->db->get()->row();
      }
      function get_tender_details($id)
      {
        $this->db->select('organization.name_'.$this->m_lang->get_language().' as organname,tenders.title_'.$this->m_lang->get_language().' as title,announced_date,closing_date,funding_agency_'.$this->m_lang->get_language().' as funding_agency,submission_guideline_'.$this->m_lang->get_language().' as submission_guideline,tenders.description_'.$this->m_lang->get_language().' as tdescription,tenders.* ')->from('tenders');
        $this->db->join('organization','organization.id=tenders.organization_id','left');
        $this->db->where('tenders.id',$id);
        return $this->db->get()->row();
      }
      function add_tender_appliers($image_name)
      {
        $data=array(
          'subject'=>htmlspecialchars($this->input->post('subject')),
          'description'=>htmlspecialchars($this->input->post('message')),
          'tender_id'=>htmlspecialchars($this->input->post('tender_id')),
          'file_name'=>htmlspecialchars($image_name),
          'user_id'=>$this->session->userdata('user_id')
        );
        $this->db->insert('tender_appliers',$data);
        if($this->db->affected_rows() > 0){
            return true;
          }else{
            return false;
        }

      }
      
	function validateuser()
    {
      $this->db->select('*')->from('vendors');
      $this->db->where('username',$this->input->post('username'));
      $this->db->where('password',md5($this->input->post('password')));
      $this->db->where('is_active', 1);
      return $this->db->get()->row();
      // ex($this->db->last_query());
    }
    function get_about_aeip()
    {
      $this->db->select('*')->from('about_aeip');
      $this->db->order_by("id", "desc");
      $this->db->limit(1);
      return $this->db->get()->row();
    }
    function get_organization_details()
    {
      $this->db->select('*')->from('organization');
      return $this->db->get()->result();
    }
    function organization_details($id)
    {
       $this->db->select('*')->from('organization');
       $this->db->where('id',$id);
        return $this->db->get()->row();
    }
    function get_username($user_id)
    {
      $this->db->select('*')->from('vendors');
      $this->db->where('id',$user_id);
      return $this->db->get()->row()->name;
    }
    function get_news()
    {
      $this->db->select("*")->from('news');
      $this->db->order_by('id', 'DESC');
      $this->db->limit('3');  
      return $this->db->get()->result();
    }

    function get_news_for_side()
    {
      $this->db->select("*")->from('news');
      $this->db->order_by('id', 'DESC');
      $this->db->limit('10');  
      return $this->db->get()->result();
    }

  function get_news_details($id){
    $this->db->select('news.*,title_'.$this->m_lang->get_language().' as title,description_'.$this->m_lang->get_language().' as description')->from('news');
    $this->db->where('id',$id);
    return $this->db->get()->row();
  }

  function private_sector_details($id = ''){
    $this->db->select('private_sector.*')->from('private_sector');
    $this->db->where('id',$id);
    return $this->db->get()->row();
  } 

  /*get Private sector projects*/
  function private_projects($id = ''){
    $this->db->select('pp.*, u.name_'.$this->m_lang->get_language().' as uname , p.name_'.$this->m_lang->get_language().' as pname');
    $this->db->from('private_projects as pp');
    $this->db->join('units as u', 'u.id = pp.units_id', 'left');
    $this->db->join('provinces as p', 'p.code = pp.province_id', 'left');
    return $this->db->where('user_id', $id)->get()->result();
  }

  // get Province
  function get_provinces(){
    return $this->db->where('CountryCode', 'AFG')->get('city')->result();
  }
  // get job Category
  function get_job_category(){
    $this->db->select('job_category.cat_name_'.$this->m_lang->get_language().' as name,job_category.id as id');
    return $this->db->get('job_category')->result();
  }

  function get_tenders($name ='',$organization_id = '',$category_id = ''){

    if ($name != '')$this->db->like('title_'.$this->m_lang->get_language(), $name);
    if ($organization_id != '')$this->db->where('organization_id',$organization_id);
    if ($category_id != '')$this->db->where('category_id', $category_id);
    return $this->db->get('tenders')->result();
    // ex($this->db->last_query());
  }
  function get_total_tender($name ='',$organization_id = '',$category_id = ''){

    if ($name != '')$this->db->like('title_'.$this->m_lang->get_language(), $name);
    if ($organization_id != '')$this->db->where('organization_id',$organization_id);
    if ($category_id != '')$this->db->where('category_id', $category_id);
    return $this->db->get('tenders')->count_all_results();
    // ex($this->db->last_query());
  }

  function get_organization(){
    $this->db->select('name_'.$this->m_lang->get_language().' as name, id');
    return $this->db->get('organization')->result();
  }

  // This function is to get Category 
  function get_project_category(){
    $this->db->select('name_'.$this->m_lang->get_language().' as name, id');
    return $this->db->get('project_category')->result();
  }

  // inserts Private sectors into DB
  function register_private_sector($upload = ''){
    $private_sector = array(
      'username' => htmlspecialchars($this->input->post('username')) , 
      'password' => md5($this->input->post('password')) , 
      'org_name' => htmlspecialchars($this->input->post('org_name')) , 
      'primary_phone' => htmlspecialchars($this->input->post('primary_phone')) , 
      'secondary_phone' => htmlspecialchars($this->input->post('secondary_phone')) , 
      'email' => htmlspecialchars($this->input->post('email')) , 
      'province' => htmlspecialchars($this->input->post('province')) , 
      'city' => htmlspecialchars($this->input->post('city')) , 
      'street' => htmlspecialchars($this->input->post('street')) , 
      'website' => htmlspecialchars($this->input->post('website')) , 
      'work_place' => htmlspecialchars($this->input->post('work_place')) , 
      'details' => htmlspecialchars($this->input->post('details')) , 
      'capabilities' => htmlspecialchars($this->input->post('capabilities')) , 
      'registration_agency' => htmlspecialchars($this->input->post('registration_agency')) , 
      'established_date' => $this->input->post('established_date'), 
      'valid_date' => $this->input->post('valid_date'), 
      'tax_payer_id' => htmlspecialchars($this->input->post('tax_payer_id')) , 
      'registration_number' => htmlspecialchars($this->input->post('registration_number')) , 
      'file' => $upload
    );
    return $this->db->insert('private_sector', $private_sector);
  }

//---Function to get Records
      function getName($table="",$value="*",$selector="",$whr="")
      {                 
        if($table!=""){ 
            $this->db->select($value);
            $this->db->from($table);           
            if($whr!=""){
                $this->db->where($selector,$whr);
            }
            $result = $this->db->get();
            /*echo($this->db->last_query());
            echo "<br/>";*/
            if($result && $result->num_rows() > 0){
                return $result;
            }else{
                return false;
            }
        }
      } 

}
?>
