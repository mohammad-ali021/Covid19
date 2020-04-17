<?php

/*
 * @desc this class insert, edit, view and ... user
 * @author: Mansour Abbasi
 * @Date: 02, June, 2016
 */
class Home extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        //$this->m_auth->notLogin();
        //load some libraries-----------------------------
        $this->load->library('form_validation');
        $this->load->library('Dateconverter');
        $this->load->library('user_auth');
        $this->load->library('ajax_pagination');
        $this->load->library("session");

        $this->load->model('ajax_model');
        $this->load->model('m_auth/users');
        $this->lang->load('global', $this->user_auth->get_language_name());
        $this->lang->load('user', $this->user_auth->get_language_name());
       
    }

    function index($id = 0)
    {
        //check the authentication
        if(!$this->user_auth->isLogedIn())
        {
            redirect('login/home/login');
        }
        else
        {

            $records = $this->users->get_user_info($id);
            if($records != '')
            {
                $data["records"] = $records->result();
            }
            else
            {
                $data["records"] = '';
            }
            putHeader();
            putTop();
            $this->load->view('user/edit_view',$data); 
            putFooter();
        }
    }

    // update the edited user's info
    function update_user_profile($userid = 0)
    {
        //check the authentication
        if(!$this->user_auth->isLogedIn())
        {
            redirect('login/home/login');
        }
        else 
        {

            $userData = array(

                    "firstname"         => $this->input->post('fname'),
                    "lastname"          => $this->input->post('lname'),
                    "username"          => $this->input->post('uname'),
                    "password"          => md5($this->input->post('pass')),

                );

            //insert data into table
            $updated = $this->users->updateAccount($userData,$userid);
            if($updated)
            {
                //echo "GOOD";exit;
                $this->session->set_flashdata('msg','<center><div class="alert alert-success"> Profile successfully updated </div></center>');
                //redirect to edit page
                redirect("user/home/getAll");
            }
            else
            {
                $this->session->set_flashdata('msg','<center><div class="alert alert-danger">There was problems updating your profile</div></center>');
                //redirect to edit page
                redirect("user/home/index/$userid");
            }

        }
    }

    // function index()
    // {
    //     $this->getAll();
    // }
    //get all observed list
    function getAll($page=0)
    {
        //check the authentication
        if(!$this->user_auth->isLogedIn())
        {
            redirect('login/home/login');
        }
        else 
        {
            //check the user role
            if($this->user_auth->isRole()==3 || $this->user_auth->isRole()==2)
            {
                //load the error page
                $this->load->view('user/restricted');
            }
            else
            {
                $str_post='ajax=1';
                $starting=$page;
                $recordPerPage = $this->config->item('recPerPage');
                //if starting posted
                if($this->input->post('starting'))
                {
                    $starting=$this->input->post('starting');
                }
                //get records from database
                $records = $this->users->getAll($starting,$recordPerPage,FALSE);
                //get total users
                $total = $this->users->getAll($starting,$recordPerPage,TRUE);
                $data['records'] = false;
                //check if record object found
                if($records)
                {
                    $data['records'] = $records;

                }
                /**
                * @desc ajax pagination configuration--------------------------------
                */

                $this->ajax_pagination->make_search(
                    $total,
                    $starting,
                    $recordPerPage,
                    $this->lang->line('global_first'),
                    $this->lang->line('global_last'),
                    $this->lang->line('global_previous'),
                    $this->lang->line('global_next'),
                    $this->lang->line('global_page'),
                    $this->lang->line('global_of'),
                    $this->lang->line('global_total'),
                    base_url().'user/home/getAll',
                    'listDiv',
                    $str_post

                );
                $data['total'] = $this->ajax_pagination->total;
                $data['links'] = $this->ajax_pagination->anchors;
                $data['page'] = $starting;
                //$data['links'] = $this->ajax_pagination->anchors;
                //-------------------------------------------------------------------
                if($this->input->post('ajax')==1)
                {   
                    $this->load->view('user/user_list',$data);
                }
                else
                {   
                    putHeader();
                    putTop();
                    //load view for showing lists
                    $this->load->view('user/user_list',$data);
                    putFooter();
                }
            }
            
        }
        
    }
    /**
     * Register new user
     */
    function editUser($id=0) 
    {
        //check the authentication
        if(!$this->user_auth->isLogedIn())
        {
            redirect('login/home/login');
        }
        else 
        {
            //check the user role
            if($this->user_auth->isRole()==3 || $this->user_auth->isRole()==2)
            {
                //load the error page
                $this->load->view('user/restricted');
            }
            else
            {
                //echo ("Here...........");exit;
                //print_r($_POST);exit;
                $this->form_validation->set_rules('firstname', 'first name', 'required');
                $this->form_validation->set_rules('username', 'username', 'required|xss_clean');
                 
                $records = $this->users->get_user_info($id);
                if($records)
                {
                    $data['records'] = $records->result_array();
                }
                else
                {
                    $data['records'] = false;
                }

                if ($this->form_validation->run() == FALSE) {

                    putHeader();
                    putTop();
                    $this->load->view('user/edit_user',$data);
                    putFooter();
                } 
                else 
                {
                    //data var for posted data
                    $postData = array(

                        "firstname" => $this->input->post('firstname'),
                        "lastname"  => $this->input->post('lastname'),
                        "email"     => $this->input->post('email'),
                        "phone"     => $this->input->post('phone'),
                        "username"  => $this->input->post('username'),
                        "region"    => $this->input->post('region'),
                        "province"  => $this->input->post('province'),
                        "type"   => $this->input->post('type'),
                        "status"   => $this->input->post('status')
                    );
                    if($this->input->post('new_pass')!='')
                    {
                        $postData['password'] = md5($this->input->post('new_pass'));
                    }
                    //insert data into table
                    $created = $this->users->updateAccount($postData,$id);
                    if($created)
                    {
                        $this->session->set_flashdata('msg','<center><div class="alert alert-success"> Record updated successfully !</div></center><br /><br />');
                        //redirect to list page
                        redirect('user/home/getAll');
                    }
                    else
                    {
                        $this->session->set_flashdata('msg','<center><div class="alert alert-danger">Error, something was wrong please contact system developer !</div></center><br /><br />');
                        //redirect to list page
                        redirect('user/home/getAll');
                    }
                }
            }
        }
        
    }
    function checkPassword()
    {
        $current = $this->input->post('pass');
        $new = md5($this->input->post('input'));
        if($current==$new)
        {
            echo "";
        }
        else
        {
            echo "Current not match";
        }
    }
    function deleteUser($id=0)
    {
        $deleted = $this->users->deleteUser($id);
        if($deleted)
        {
            $this->session->set_flashdata('msg','<center><div class="alert alert-success">Record No # <b style="color:red">'.$id.'</b> Successfuly Deleted !</div></center><br /><br />');
                    
            //redirect to list page
            redirect('user/home/getAll');
                    
        }
        else
        {
            $this->session->set_flashdata('msg','<center><div class="alert alert-danger">There is a problem with deleting the record !</div></center><br /><br />');
                    
            //redirect to list page
            redirect('user/home/getAll');
        }
    }
    
}
    

?>
