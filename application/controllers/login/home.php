<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @desc: Login Controller
 * @author Mansour Abbasi
 * @date: 01-June-2016
 * @version: 0.01
 */
class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('user_auth');
        $this->load->library('form_validation');
        $this->load->model('m_auth/users');
        $this->lang->load('global',$this->user_auth->get_language_name());
        $this->lang->load('user',$this->user_auth->get_language_name());
      
    }

    /**
     * Register new user
     */
    public function signup() 
    {
        
        $this->form_validation->set_rules('firstname', 'First Name', 'required');
        $this->form_validation->set_rules('lastname', 'Last Name', 'required');
        $this->form_validation->set_rules('organization', 'Organization', 'required');
        $this->form_validation->set_rules('username', 'User Name', 'required|xss_clean|is_unique[users.username]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|min_length[4]|matches[confirm_password]|md5');
        $this->form_validation->set_rules('confirm_password','Password Confirmation', 'trim|required|md5'); 
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|is_unique[users.email]');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|xss_clean');
        $this->form_validation->set_rules('region', 'Region', 'required');
        $this->form_validation->set_rules('province', 'Province', 'required');
        if ($this->form_validation->run() == FALSE) {

            putHeader();
            putTop();
            $this->load->view('user/signup');
            putFooter();
        } 
        else 
        {
            //data var for posted data
            $postData = array(
                
                "firstname"     => $this->input->post('firstname'),
                "lastname"      => $this->input->post('lastname'),
                "organization"  => $this->input->post('organization'),
                "email"         => $this->input->post('email'),
                "phone"         => $this->input->post('phone'),
                "region"        => $this->input->post('region'),
                "province"      => $this->input->post('province'),
                "username"      => $this->input->post('username'),
                "password"      => $this->input->post('password'),
                "enckey"        => md5(rand(99,999999999999999999)),
                "type"          => 3
            );
            //insert data into table
           /* echo "<pre>";
            print_r($postData);exit;*/
            $created = $this->users->createAccount($postData);
            if($created)
            {
                $to_email=$this->users->getValue('users','email',' id='.$created);
                $key=$this->users->getValue('users','enckey',' id='.$created);
                if($this->sendEmail($to_email,$key)){
                    $data['created'] = "Account successfully created and it is pending for activation by email !";
                    putHeader();
                    putTop();
                    $this->load->view('user/created', $data);
                }else{
                    $data['created'] = "Error, something was wrong, please contact system developer !";
                    $this->load->view('user/created',$data);
                }
            }
            else
            {
                putHeader();
                putTop();
                $data['created'] = "Error, something was wrong, please contact system developer !";
                $this->load->view('user/created',$data);
                putFooter();
            }
        }
    }
    
    public function forget() 
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            putHeader();
            putTop();
            $this->load->view('user/forgetpass');
        } 
        else 
        {
            //insert data into table
            $email=$this->input->post('email');
            $check=$this->users->getValue('users','email',' email="'.$email.'"');
            if($check == false){
               $this->session->set_flashdata('msg','<div class="alert alert-danger"><h5>Email not exist, please try agin or <a href="'.base_url().'login/home/signup">sign up</a></h5></div>');
              //--- redirect to Forget password and set a flash message ---
               redirect('login/home/forget');
            }else{
                $id=$this->users->getId('users',' email="'.$email.'"');
                $username=$this->users->getValue('users','username',' id='.$id.'');
                $postData = array(
                    "userid"        => $id,
                    "email"         => $email,
                    "enckey"        => md5(rand(99,999999999999999999)),
                    "used"          => 0,
                    "resetdate"     => date('Y-m-d H:i:s'),
                );
                $created = $this->users->resetpassword($postData);
                if($created)
                {
                    $to_email=$email;
                    $key=$this->users->getValue('reset','enckey',' id='.$created);
                    if($this->sendEmail($to_email,$key,2,$username)){
                        $data['created'] = "The link to reset your password has been sent to your mail. Kindly check your mail to reset the password.";
                        putHeader();
                        putTop();
                        $this->load->view('user/created', $data);
                    }else{
                        $data['created'] = "Error, something was wrong, please try again.";
                        $this->load->view('user/created',$data);
                    }
                }
                else
                {
                    putHeader();
                    putTop();
                    $data['created'] = "Error, something was wrong, please contact system developer !";
                    $this->load->view('user/created',$data);
                }
            }
            
        }
    }
    
    
    function sendEmail($to_email,$key,$target=0,$username='')
    {
        $this->load->library('email');
        $from_email = 'support@healthemergency.info';
        if ($target == 2 ){
            $subject = 'Reset Password';
            $message = 'Dear User,<br /><br />Your user name is : <strong>'.$username.'</strong> <br/> Please click on the below reset link to reset your password.<br /><br /> http://healthemergency.info/login/home/resetpasswrod/' .$key. '<br /><br /><br />Thanks<br />Afghanistan Health Emergnecy Team (WHE)';   
        }else  {
            $subject = 'Verify Your Email Address';
            $message = 'Dear User,<br /><br />Please click on the below activation link to verify your email address.<br /><br /> http://healthemergency.info/login/home/verify/' .$key. '<br /><br /><br />Thanks<br />Consileon Team';
        }
        
        //configure email settings
        $config['protocol']  = 'sendmail';
        $config['smtp_host'] = 'mail.healthemergency.info'; //smtp host name
        $config['smtp_port'] = '465'; //smtp port number
        $config['smtp_user'] = $from_email;
        $config['smtp_pass'] = 'no-rep'; //$from_email password
        $config['mailtype'] = 'html';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = TRUE;
        $config['newline'] = "\r\n"; //use double quotes
        $this->email->initialize($config);
        
        //send mail
        $this->email->from($from_email, 'www.healthemergency.info');
        $this->email->to($to_email);
        $this->email->subject($subject);
        $this->email->message($message);
        if($this->email->send()){
            return true ;
        }else{
            return false ;
        }
    }
    function verify($key=''){
        
        $verify=$this->users->verify($key);
        if($verify!=''){
            putHeader();
            putTop();
            $data['created'] = "Congratulation, Your Account Has Been Successfully Verified!";
            $this->load->view('user/created',$data);
        }
    }
    
    function resetpasswrod($key=''){
        
        $verify=$this->users->verifyPassword($key);
        if($verify == true ){
            $userId=$this->users->getValue('reset','userid','enckey="'.$key.'"');
            $data['userid']=$userId;
            putHeader();
            putTop();
            $this->load->view('user/resetForm',$data);
        }else {
            $this->session->set_flashdata('msg','<div class="alert alert-danger"><h5>Invalid rest link!</h5></div>');
              //--- redirect to Forget password and set a flash message ---
            redirect('login/home/forget');
        }
    }
    function resetPass(){
        
        $id=$this->input->post('uid');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|min_length[4]|matches[confirm_password]|md5');
        if ($this->form_validation->run() == FALSE) {
            $data['userid']=$id;
            putHeader();
            putTop();
            $this->load->view('user/resetForm',$data);
        }else{
            $postData = array(
            "password"      => $this->input->post('password'),
            );
            $id=substr($id,4);
            if($this->users->updateAccount($postData,$id)){
                $this->session->set_flashdata('msg','<div class="alert alert-success"><h5>Password has been successfully updated.</h5></div>');
                redirect('login/home/login');
            }else{
                $this->session->set_flashdata('msg','<div class="alert alert-danger"><h5>An error occurred, please try again </h5></div>');
                redirect('login/home/login');
            }
        }
        
    }
    
    /**
     * 
     * @param type $type
     */
    function login() {

            if(!$this->session->userdata('language'))
            {
                $this->session->set_userdata('language','en');
            }
            
            $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
            if($this->form_validation->run()==FALSE)
            {
                $this->load->view('user/login');
            }
            else
            {
                //check the username and password if exist
                if($this->isUserValid($this->input->post('username'),$this->input->post('password')))
                {
                    $ip='';
                    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
                        $ip=$_SERVER['HTTP_CLIENT_IP'];
                    }
                    elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
                      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
                    }
                    else{
                      $ip=$_SERVER['REMOTE_ADDR'];
                    }
                    //get log data
                    $LogData = array(
                        
                        'username'      => $this->input->post('username'),
                        'ip_address'    => $ip,
                        'server_name'   => $_SERVER['SERVER_NAME'],
                        'login_date'    => date('Y-m-d H:i:s')
                    );
                    //insert log user data to db
                    $this->users->insertUserLog($LogData);
                    
                    redirect("main/home");
                    
                }
                else
                {
                    $this->session->unset_userdata('username');
                    $this->session->unset_userdata('userid');
                    $this->session->unset_userdata('database');
                    $this->session->set_flashdata('invalid',"Username or password is not correct! Please try again.");
                    redirect("login/home/login");
                }
            } 
    }
    
    
    function isUserValid($username='',$password='')
    {
        //check
        $valid = $this->users->isValid($username,md5($password));
        if($valid)
        {
            $userid = $this->users->get_user_by_username($username);
            
            $this->session->set_userdata(array('username' => $username,'userid' => $userid->row()->id,'user_type' => $userid->row()->type,'database' => $userid->row()->database));
            // return true;
            if($userid && $userid->row()->status==1)
            {
                $this->session->set_userdata(array('username' => $username,'userid' => $userid->row()->id,'user_type' => $userid->row()->type,'database' => $userid->row()->database));
                return true;
            }
           else
           {
               $this->session->set_flashdata('invalid',"Your account is not activated, Please contact administrator.");
               redirect('login/home/login');
           }
            
        }
        else
        {
            return false;
        }
    }

    /**
     * Logout User
     */
    function logout() {
        $this->session->sess_destroy();
        redirect('login/home/login','refresh');
        //$this->_show_message($this->lang->line('auth_message_logged_out'));
    }

    
    public function changeLanguage($lang='en') {
        if($this->input->post('lang'))
        {
            $lang = $this->input->post('lang');
        }
        //$this->m_auth->set_language($this->input->post('lang'));
        $this->session->set_userdata('language',  $lang);
        $this->session->set_userdata('lang_active',  $lang);
        //return TRUE;
        redirect($_SERVER['HTTP_REFERRER'],'refresh');
    }

}//----End of class---//