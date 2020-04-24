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
    }
    
    function index($page=0){
                //load view for showing lists
                $this->load->view('dashboard');
        }

    function privacyPolicy(){
        $this->load->view('privicyPolicy');
    }

}
?>
