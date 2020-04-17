<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 *  ======================================= 
 *  Author     : Gul Muhammad 
 *  License    : Protected 
 *  Email      : gm.akbari27@gmail.com 
 *   
 *  Dilarang merubah, mengganti dan mendistribusikan 
 *  ulang tanpa sepengetahuan Author 
 *  ======================================= 
 */  
require_once APPPATH."/third_party/PHPExcel.php"; 

class Excel extends PHPExcel { 
    public function __construct() { 
        parent::__construct(); 
    } 
}