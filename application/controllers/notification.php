<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');


class Notification extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('notification_model');

		
	}

	public function test($token ){
		$this->notification_model->send_ios($token, 'Test Message', array('custom_var' => 'val'));
	}
}

