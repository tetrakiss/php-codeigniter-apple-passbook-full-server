<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH ."/third_party/PKPass.php"; 
 
class PKPass extends PKPass { 
    public function __construct() { 
        parent::__construct(); 
    } 
}

/***** End of BCrypt.php ***********/