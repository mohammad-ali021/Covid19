<?php
  class Home_model extends CI_Model{
      
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
          //print_r($data);exit;
            $this->db->trans_start();  
            $this->db->insert_batch($table,$data);
            $this->db->trans_complete();
            return true;
        }
      }   
      
      //---Function to get Records
      function getRecords($table="",$whr="",$select="",$groupby="")
      {
        if($table!=""){
            if ($select !=""){
                 $this->db->select($select);
            }else{
            $this->db->select("*");
            }
            $this->db->from($table);
            if($whr!=""){
                $this->db->where($whr);
            }
            if($groupby!=""){
                $this->db->group_by($groupby);
            }
            $result = $this->db->get();
            //echo ($this->db->last_query());
            if($result && $result->num_rows() > 0){
                return $result;
            }else{
                return false;
            }
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

      function getAll($table,$whr,$offset=0,$per=0,$total=FALSE)
        {
            $this->db->select("*");
            $this->db->from($table);
            if($whr!=""){
                $this->db->where($whr);
            }
            if($total!=TRUE)
            {
                $this->db->limit($per,$offset);
            }
            $object = $this->db->get();
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
  }
?>
