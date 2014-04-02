<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');


class Notification extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('notification_model');

		
	}

	public function test($token ){
		//$this->notification_model->send_ios($token, 'CORSOCOMO', array('key' => 'expires', 'value' => '2013-06-02T10:00-05:00', 'changeMessage' => 'Date changed to %@.'));
		$this->notification_model->send_ios($token, 'CORSOCOMO');
	}
}

