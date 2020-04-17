<?php

class User_auth {

    private $error = array();

    function __construct() {
        $this->ci = & get_instance();
        $this->ci->load->library('session', 'database');
        $this->ci->load->database();
        $this->ci->load->model('m_auth/users');

    }
    /**
     * 
     * @return boolean check if a user is logged in or not
     */
    function isLogedIn() {
        $this->ci = & get_instance();
        if ($this->ci->session->userdata('username')) 
        {
            return true;
            
        }
        else
        {
            //echo "pelase <a href='login'><b>login</b></a> to continue ";
            redirect('login/home/login', 'refresh');
            
        }
    }
    /**
     * Get user_id
     *
     * @return	string
     */
    function get_user_id() {
        return $this->ci->session->userdata('userid');
    }
    /**
     * Get username
     *
     * @return	string
     */
    function get_username() {
        return $this->ci->session->userdata('username');
    }

    /**
     * 
     * @return type
     */
    function isRole() {
        return $this->ci->session->userdata('user_type');
    }

    function get_user_by_id($id) {
        $this->ci->db->where('id', $id);
        $row = $this->ci->db->get('users');
        if ($row->num_rows()) {
            return $row->row()->username;
        }
    }
    /**
     * 
     * @return type
     */
    function get_userEmail() {
        $this->ci->db->where('id', $this->get_user_id());
        return $this->ci->db->get('users')->row()->email;
    }
    function get_userRegion() {
        $this->ci->db->where('id', $this->get_user_id());
        return $this->ci->db->get('users')->row()->region;
    }
    
    function getModuleRole($user,$module_code=''){
        if($user==''){
            return false;
        }else{
            $whr='user_id = '.$user.' AND module_id ="'.$module_code.'"';
            $this->ci->db->where($whr);
            $result= $this->ci->db->get('user_access');
            if($result && $result->num_rows()>0){
                return true;
            }else{
                return false;
            }
        }
        
    }
    
    function get_userProvince() {
        $this->ci->db->where('id', $this->get_user_id());
        return $this->ci->db->get('users')->row()->province;
    }
    
    /**
     * Check if username available for registering.
     * Can be called for instant form validation.
     *
     * @param	string
     * @return	bool
     */
    function is_username_available($username) {
        return ((strlen($username) > 0) AND $this->ci->users->is_username_available($username));
    }
    /**
     * 
     * @return type
     */
    public function get_language() {
        return $this->ci->session->userdata('language');
    }
    /**
     * 
     * @param type $lang
     */
    public function set_language($lang) {
        $this->ci->session->set_userdata(array('language'=> $lang));
    }
    /**
     * 
     * @return string
     */
    public function get_language_name() {
        $lang = $this->get_language();

        if ($lang == 'dr') {
            $language = "dari";
        } else if ($lang == 'pa') {
            $language = "pashto";
        } else {
            $language = "english";
        }
        return $language;
    }
}
?>
