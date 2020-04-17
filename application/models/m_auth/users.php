<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * M_auth
 *
 * Authentication library for CodeIgniter.
 *
 * @package        M_auth
 * @author         Mansour Abbasi
 * @version        1.0.0
 * @Email          <>
 */
class Users extends CI_Model {

    private $table_name = 'users';          // user accounts
    private $profile_table_name = 'user_profiles'; // user profiles

    function __construct() {
        parent::__construct();

        $ci = & get_instance();
        $this->table_name = $ci->config->item('db_table_prefix', 'm_auth') . $this->table_name;
        $this->profile_table_name = $ci->config->item('db_table_prefix', 'm_auth') . $this->profile_table_name;
    }

    /**
     * Get user record by Id
     *
     * @param	int
     * @param	bool
     * @return	object
     */
    function get_user_by_id($user_id, $activated) {
        $this->db->where('id', $user_id);
        $this->db->where('activated', $activated ? 1 : 0);
        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }
    function insertUserLog($data=array())
    {
        if(!empty($data))
        {
            $this->db->insert('auth_log',$data);
        }
    }
    /**
     * Get user record by login (username or email)
     *
     * @param	string
     * @return	object
     */
    function get_user_by_login($login) {
        $this->db->where('LOWER(username)=', strtolower($login));
        $this->db->or_where('LOWER(email)=', strtolower($login));

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    /**
     * Get user record by username
     *
     * @param	string
     * @return	object
     */
    function get_user_by_username($username) {
        $this->db->where('LOWER(username)=', strtolower($username));
        //$this->db->where("status",1);
        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query;
        return NULL;
    }

    /**
     * Get user record by email
     *
     * @param	string
     * @return	object
     */
    function get_user_by_email($email) {
        $this->db->where('LOWER(email)=', strtolower($email));

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    /**
     * Check if username available for registering
     *
     * @param	string
     * @return	bool
     */
    function is_username_available($username) {
        $this->db->select('1', FALSE);
        $this->db->where('LOWER(username)=', strtolower($username));
        $query = $this->db->get($this->table_name);
        return $query->num_rows() == 0;
    }

    /**
     * Check if email available for registering
     *
     * @param	string
     * @return	bool
     */
    function is_email_available($email) {
        $this->db->select('1', FALSE);
        $this->db->where('LOWER(email)=', strtolower($email));
        $this->db->or_where('LOWER(new_email)=', strtolower($email));

        $query = $this->db->get($this->table_name);
        return $query->num_rows() == 0;
    }

    /**
     * Create new user record
     *
     * @param	array
     * @param	bool
     * @return	array
     */
    function create_user($data, $activated = TRUE) {
        $data['created'] = date('Y-m-d H:i:s');
        $data['activated'] = $activated ? 1 : 0;
        $this->db->trans_start();
        /*
          if ($this->db->insert($this->table_name, $data)) {
          $user_id = $this->db->insert_id();

          if($activated)
          {
          $this->create_profile($user_id);
          return array('user_id' => $user_id);
          }
          }
          return NULL;
         * 
         */
        $this->db->insert($this->table_name, $data);
        $userid = $this->db->insert_id();
        $this->db->trans_complete();
        return $userid;
    }
    function createAccount($data=array())
    {
        
        $this->db->trans_start();
        $created = $this->db->insert($this->table_name,$data);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        if($insert_id && $insert_id!=0)
        {
            return $insert_id;
        }
        else
        {
            return false;
        }
    }
    
    function resetpassword($data=array())
    {
        
        $this->db->trans_start();
        $created = $this->db->insert('reset',$data);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        if($insert_id && $insert_id!=0)
        {
            return $insert_id;
        }
        else
        {
            return false;
        }
    }
   
    function getValue($table,$column,$whr){
        $query="SELECT ".$column." FROM ".$table." WHERE ".$whr;
        $query=$this->db->query($query);
        if($query){
            $something= $query->row();
            return $something->$column;
        }else{
            return false;
        }
    }
    
    function getId($table,$whr){
        $query="SELECT id FROM ".$table." WHERE ".$whr;
        $query=$this->db->query($query);
        if($query){
            $something= $query->row();
            return $something->id;
        }else{
            return false;
        }
    }
    
    function verify($key){
        $this->db->trans_start();
        $result=$this->db->get_where('users',array('enckey =' => $key));
        $this->db->trans_complete();
        if($result->num_rows()>0){
            $this->db->where('enckey',$key);
            $this->db->update('users',array("status" => 1));
            return true;
        }else{
            return false;
        }
    }
    
    function verifyPassword($key){
        $this->db->trans_start();
        $result=$this->db->get_where('reset',array('enckey =' => $key));
        $this->db->trans_complete();
        if($result->num_rows()>0){
            $status=$this->getValue('reset','used','enckey="'.$key.'"');
            //echo $status;exit;
            if($status==0){
                $this->db->trans_start();
                $this->db->where('enckey',$key);
                $this->db->update('reset',array("used" => 1));
                $this->db->trans_complete();
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    function updateAccount($data=array(),$id=0)
    {
        $this->db->where('enckey',$id);
        $updated = $this->db->update($this->table_name,$data);
        if($updated)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    /**
     * Get User detaisl
     * After inserted
     */
    public function get_user_details_after($user_id = 0) {
        if ($user_id != 0) {
            $this->db->select('t1.*');
            $this->db->from('users AS t1');
            $this->db->where('t1.id', $user_id);
            $query = $this->db->get();
            if ($query) {
                return $query;
            } else {
                return FALSE;
            }
        }
    }

    /**
     * Activate user if activation key is valid.
     * Can be called for not activated users only.
     *
     * @param	int
     * @param	string
     * @param	bool
     * @return	bool
     */
    function activate_user($user_id, $activation_key, $activate_by_email) {
        $this->db->select('1', FALSE);
        $this->db->where('id', $user_id);
        if ($activate_by_email) {
            $this->db->where('new_email_key', $activation_key);
        } else {
            $this->db->where('new_password_key', $activation_key);
        }
        $this->db->where('activated', 0);
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1) {

            //$this->db->set('activated', 1);
            //$this->db->set('new_email_key', NULL);
            //$this->db->where('id', $user_id);
            $data = array(
                'activated' => 1,
                'new_email_key' => NULL,
            );
            $this->db->where('id', $user_id);
            $this->db->update($this->table_name, $data);


            $op_data = array(
                'ip' => $this->input->ip_address(),
                'js_op_by' => $this->m_auth->get_user_id(),
                'dated' => date('Y-m-d H:i:s'),
                'js_table' => $this->table_name,
                'js_tab_op_id' => $user_id,
                'js_op' => " Activated ",
                'op_type' => 'Create',
            );
            $this->db->insert('js_ops', $op_data);


            $this->create_profile($user_id);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Purge table of non-activated users
     *
     * @param	int
     * @return	void
     */
    function purge_na($expire_period = 172800) {
        $this->db->where('activated', 0);
        $this->db->where('UNIX_TIMESTAMP(created) <', time() - $expire_period);
        $this->db->delete($this->table_name);
        return TRUE;
    }

    /**
     * Delete user record
     *
     * @param	int
     * @return	bool
     */
    function delete_user($user_id) {
        $this->db->where('id', $user_id);
        $this->db->delete($this->table_name);
        if ($this->db->affected_rows() > 0) {
            $this->delete_profile($user_id);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Set new password key for user.
     * This key can be used for authentication when resetting user's password.
     *
     * @param	int
     * @param	string
     * @return	bool
     */
    function set_password_key($user_id, $new_pass_key) {
        $this->db->set('new_password_key', $new_pass_key);
        $this->db->set('new_password_requested', date('Y-m-d H:i:s'));
        $this->db->where('id', $user_id);

        $this->db->update($this->table_name);


        $op_data = array(
            'ip' => $this->input->ip_address(),
            'js_op_by' => $this->m_auth->get_user_id(),
            'dated' => date('Y-m-d H:i:s'),
            'js_table' => $this->table_name,
            'js_tab_op_id' => $user_id,
            'js_op' => "Set password key",
            'op_type' => 'Create',
        );
        $this->db->insert('js_ops', $op_data);


        return $this->db->affected_rows() > 0;
    }

    /**
     * Check if given password key is valid and user is authenticated.
     *
     * @param	int
     * @param	string
     * @param	int
     * @return	void
     */
    function can_reset_password($user_id, $new_pass_key, $expire_period = 900) {
        $this->db->select('1', FALSE);
        $this->db->where('id', $user_id);
        $this->db->where('new_password_key', $new_pass_key);
        $this->db->where('UNIX_TIMESTAMP(new_password_requested) >', time() - $expire_period);

        $query = $this->db->get($this->table_name);
        return $query->num_rows() == 1;
    }

    /**
     * Change user password if password key is valid and user is authenticated.
     *
     * @param	int
     * @param	string
     * @param	string
     * @param	int
     * @return	bool
     */
    function reset_password($user_id, $new_pass, $new_pass_key, $expire_period = 900) {
        $this->db->set('password', $new_pass);
        $this->db->set('new_password_key', NULL);
        $this->db->set('new_password_requested', NULL);
        $this->db->where('id', $user_id);
        $this->db->where('new_password_key', $new_pass_key);
        $this->db->where('UNIX_TIMESTAMP(new_password_requested) >=', time() - $expire_period);

        $this->db->update($this->table_name);

        $op_data = array(
            'ip' => $this->input->ip_address(),
            'js_op_by' => $this->m_auth->get_user_id(),
            'dated' => date('Y-m-d H:i:s'),
            'js_table' => $this->table_name,
            'js_tab_op_id' => $user_id,
            'js_op' => " Password Reset",
            'op_type' => 'Create',
        );
        $this->db->insert('js_ops', $op_data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Change user password
     *
     * @param	int
     * @param	string
     * @return	bool
     */
    function change_password($user_id, $new_pass) {
        $this->db->set('password', $new_pass);
        $this->db->where('id', $user_id);

        $this->db->update($this->table_name);


        $op_data = array(
            'ip' => $this->input->ip_address(),
            'js_op_by' => $this->m_auth->get_user_id(),
            'dated' => date('Y-m-d H:i:s'),
            'js_table' => $this->table_name,
            'js_tab_op_id' => $user_id,
            'js_op' => "Password Changed",
            'op_type' => 'Create',
        );
        $this->db->insert('js_ops', $op_data);


        return $this->db->affected_rows() > 0;
    }

    /**
     * Set new email for user (may be activated or not).
     * The new email cannot be used for login or notification before it is activated.
     *
     * @param	int
     * @param	string
     * @param	string
     * @param	bool
     * @return	bool
     */
    function set_new_email($user_id, $new_email, $new_email_key, $activated) {
        $this->db->set($activated ? 'new_email' : 'email', $new_email);
        $this->db->set('new_email_key', $new_email_key);
        $this->db->where('id', $user_id);
        $this->db->where('activated', $activated ? 1 : 0);

        $this->db->update($this->table_name);


        $op_data = array(
            'ip' => $this->input->ip_address(),
            'js_op_by' => $this->m_auth->get_user_id(),
            'dated' => date('Y-m-d H:i:s'),
            'js_table' => $this->table_name,
            'js_tab_op_id' => $user_id,
            'js_op' => " New email key",
            'op_type' => 'Create',
        );
        $this->db->insert('js_ops', $op_data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Activate new email (replace old email with new one) if activation key is valid.
     *
     * @param	int
     * @param	string
     * @return	bool
     */
    function activate_new_email($user_id, $new_email_key) {
        $this->db->set('email', 'new_email', FALSE);
        $this->db->set('new_email', NULL);
        $this->db->set('new_email_key', NULL);
        $this->db->where('id', $user_id);
        $this->db->where('new_email_key', $new_email_key);

        $this->db->update($this->table_name);


        $op_data = array(
            'ip' => $this->input->ip_address(),
            'js_op_by' => $this->m_auth->get_user_id(),
            'dated' => date('Y-m-d H:i:s'),
            'js_table' => $this->table_name,
            'js_tab_op_id' => $user_id,
            'js_op' => " Activate new email ",
            'op_type' => 'Create',
        );
        $this->db->insert('js_ops', $op_data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Update user login info, such as IP-address or login time, and
     * clear previously generated (but not activated) passwords.
     *
     * @param	int
     * @param	bool
     * @param	bool
     * @return	void
     */
    function update_login_info($user_id, $record_ip, $record_time) {
        $this->db->set('new_password_key', NULL);
        $this->db->set('new_password_requested', NULL);

        if ($record_ip)
            $this->db->set('last_ip', $this->input->ip_address());
        if ($record_time)
            $this->db->set('last_login', date('Y-m-d H:i:s'));

        $this->db->where('id', $user_id);
        $this->db->update($this->table_name);
    }

    /**
     * Ban user
     *
     * @param	int
     * @param	string
     * @return	void
     */
    function ban_user($user_id, $reason = NULL) {
        $this->db->where('id', $user_id);
        $this->db->update($this->table_name, array(
            'banned' => 1,
            'ban_reason' => $reason,
        ));
    }

    /**
     * Unban user
     *
     * @param	int
     * @return	void
     */
    function unban_user($user_id) {
        $this->db->where('id', $user_id);
        $this->db->update($this->table_name, array(
            'banned' => 0,
            'ban_reason' => NULL,
        ));
    }

    /**
     * Create an empty profile for a new user
     *
     * @param	int
     * @return	bool
     */
    private function create_profile($user_id) {
        //$this->db->set('user_id', $user_id);
        $data = array(
            'user_id' => $user_id
        );
        $this->db->trans_start();
        $this->db->insert($this->profile_table_name, $data);
        $this->db->trans_complete();
        return TRUE;
    }

    /**
     * Delete user profile
     *
     * @param	int
     * @return	void
     */
    private function delete_profile($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->delete($this->profile_table_name);
    }

    /**
     * Register User details
     */
    public function add_user_details($data = array()) {
        if (is_array($data)) {
            $this->db->trans_start();
            $this->db->insert('user_details', $data);
            //$id = $this->db->insert_id();
            $this->db->trans_complete();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Add Sectors and sub sectors and activity of user
     */
    public function add_user_sectors($sector_arr, $userid = 0) {
        if ($userid != 0) {
            $this->db->trans_start();
            $this->db->insert('user_sectors', $sector_arr);
            //$ins_id = $this->db->insert_id();
            $this->db->trans_complete();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Add User Sub Sectors
     */
    public function add_user_sub_sectors($sub_sec_arr, $userid = 0) {
        if ($userid != 0) {
            foreach ($sub_sec_arr AS $sub_sec) {
                $sub_array = array(
                    'userid' => $userid,
                    'sub_sector_id' => $sub_sec
                );
                $this->db->insert('user_sub_sectors', $sub_array);
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Add User Activity
     */
    public function add_user_activity($activity_arr, $userid = 0) {
        if ($userid != 0) {
            foreach ($activity_arr AS $act) {
                $act_array = array(
                    'userid' => $userid,
                    'sector_activity_id' => $act
                );
                $this->db->insert('user_sector_activity', $act_array);
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * @param type $userid
     * @return boolean
     */
    public function get_company_name_by_userid($userid = 0) {
        $this->db->select('t1.company_name');
        $this->db->from('biz_user_details AS t1');
        $this->db->where('t1.userid', $userid);
        $query = $this->db->get();
        if ($query) {
            if ($query->num_rows() > 0) {
                return $query->row()->company_name;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * bring totoal for each sectors
     */
    public function bring_total_projects($sector_id) {
        $this->db->select('t1.proj_id');
        $this->db->from('biz_projects AS t1, biz_s_sector AS t2');
        $this->db->where('t1.proj_expiry_date >=', date('Y-m-d'));
        $this->db->where('t1.sector = t2.id');
        $this->db->where('t1.sector', $sector_id);

        $query = $this->db->get();
        if ($query) {
            if ($query->num_rows() > 0) {
                return $query->num_rows();
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * bring totoal for each sectors
     */
    public function bring_total_by_location($loc_id) {
        $this->db->select('t1.proj_id');
        $this->db->from('biz_projects AS t1, biz_province AS t2');
        $this->db->where('t1.proj_expiry_date >=', date('Y-m-d'));
        $this->db->where("t1.pro_loc_id = t2.province_id");
        //$this->db->where("t1.pro_loc_id",$loc_id);
        $this->db->where("t2.province_id", $loc_id);
        $query = $this->db->get();

        if ($query) {
            if ($query->num_rows() > 0) {
                return $query->num_rows();
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * Get User info
     * after creating user
     */
    function get_user_info($userid = 0) {
        if ($userid != 0) {
            $this->db->select('t1.*');
            $this->db->from($this->table_name . " AS t1");
            $this->db->where('t1.enckey', $userid);
            $query = $this->db->get();
            //echo ($this->db->last_query());exit;
            if ($query) {
                if ($query->num_rows() > 0) {
                    return $query;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    
    /**
     * Get Tax percentage from tax cat id
     */
    function get_tax($tax_cat_id=0)
    {
        if($tax_cat_id!=0)
        {
            $this->db->select('t1.value');
            $this->db->from('h_s_tax_category AS t1');
            $this->db->where('t1.id',$tax_cat_id);
            $query = $this->db->get();
            if($query)
            {
                return $query->row()->value;
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }
    function isValid($username='',$password='')
    {
        $this->db->select('*');
        $this->db->from($this->table_name);
        $this->db->where('username',$username);
        $this->db->where('password',$password);
        $object = $this->db->get();
        if($object && $object->num_rows()>0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    function getAll($offset=0,$per=0,$total=FALSE)
    {
        $this->db->select("*");
        $this->db->from('users');
        
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
    
    //delete user
    function deleteUser($id=0)
    {
        $this->db->where('id',$id);
        $deleted=$this->db->delete('users');
        if($deleted)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

}

//-----end of class-------//

/* End of file users.php */
/* Location: ./application/models/auth/users.php */