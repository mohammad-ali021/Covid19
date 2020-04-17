<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @desc: main controller
 * @author Mohammad Ali Abassi
 * @date: 02-Jan-2019
 * @version: 0.01
 */
class Main extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('user_auth');
        $this->lang->load('global',$this->user_auth->get_language_name());
        $this->lang->load('form',$this->user_auth->get_language_name());
        $this->load->model("province_model", "province_model");
        $this->load->model('home_model');
        $this->load->library('ajax_pagination');
    }
    
    function index($page=0){
                //load view for showing lists
                $this->load->view('dashboard');
        }

}
?>
