<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');


class Passbook extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('Passbook_model', 'passes');
		 $this->load->helper(array('ssl_helper'));
		 $this->config->load('passbook', TRUE);
	}


	//public function connect($version, $devices, $deviceId = NULL , $registrations = NULL, $passTypeId, $serialNo ){
		public function connect(){
		$version=$this->uri->segment(3, 0);
		$devices=$this->uri->segment(4, 0);
		if($devices == "devices"){
		$deviceId=$this->uri->segment(5, 0);
		$registrations=$this->uri->segment(6, 0);
		$passTypeId=$this->uri->segment(7, 0);
		$serialNo=$this->uri->segment(8, 0);
		}
		if($devices == "passes"){		
		$passTypeId=$this->uri->segment(5, 0);
		$serialNo=$this->uri->segment(6, 0);
		}
		
		
		
		// force_ssl();
		$method = strtolower($this->input->server('REQUEST_METHOD'));

		if ($method == "post") {
			if ($devices == "devices") {
				$this->register_pass($version, $devices, $deviceId , $registrations, $passTypeId, $serialNo);
			}else if ($devices == "log") {
				$this->log($version, $devices);
			}
		}else if ($method == "get") {
			if ($devices == "devices") {
				// version/devices/deviceLibraryIdentifier/registrations/passTypeIdentifier?passesUpdatedSince=tag
				$this->return_serials($version, $devices, $deviceId , $registrations, $passTypeId);
			}else if ($devices == "passes"){
				// version/passes/passTypeIdentifier/serialNumber
				//$passTypeId = $deviceId;
				//$serialNo = $registrations;
				$this->return_pass($version, "passes", $passTypeId, $serialNo);
			}
		}else if ($method == "delete") {
			$this->delete_pass($version, $devices, $deviceId , $registrations, $passTypeId, $serialNo);
		}
	}

	public function register_pass($version, $devices, $deviceId , $registrations, $passTypeId, $serialNo) {
		$authenticaton_token = end(explode(' ', $this->input->get_request_header('Authorization')));
		if (!$this->passes->is_valid_token($authenticaton_token, $serialNo)) {
			$this->output->set_status_header(401);
			exit;
		}

		if ($this->passes->is_device_already_registered($deviceId, $serialNo)){
			$this->output->set_status_header(200);exit;
		}else {
			$payload = json_decode(file_get_contents("php://input"), true);
			 error_log($_POST);
			$push_token = $payload['pushToken'];

			$insert_data['device_id'] = $deviceId;
			$insert_data['serial_number'] = $serialNo;
			$insert_data['pass_type_id'] = $passTypeId;
			$insert_data['push_token'] = $push_token;
			$this->passes->register_pass($insert_data);
			$this->output->set_status_header(201);exit;
		}
	}

	//send updated serials to apple
	public function return_serials($version, $devices, $deviceId , $registrations, $passTypeId) {
		if (!$this->passes->is_valid_device($deviceId)) {
			$this->output->set_status_header(404);exit;
		}
		$passesUpdatedSince = isset($_GET['passesUpdatedSince']) ? $_GET['passesUpdatedSince'] : "";
		$passes = $this->passes->get_device_passes($deviceId, $passTypeId, $passesUpdatedSince);
		if (count($passes) > 0) {
			$response_array = array(
				'lastUpdated' => date(DATE_ATOM),
				'serialNumbers' => $passes
				);
			header('Last-modified:' .date('r'));
			echo json_encode($response_array);
		}else{
			$this->output->set_status_header(204);exit;
		}
	}

	// return apple pass
	public function return_pass($version, $passes, $passTypeId, $serialNo) {
		$authenticaton_token = end(explode(' ', $this->input->get_request_header('Authorization')));
		if (!$this->passes->is_valid_token($authenticaton_token, $serialNo)) {
			$this->output->set_status_header(401);
			exit;
		}
		/*list($ft_order_id, $ticket_num) = explode('-', $serialNo);
		header('Last-modified:' .date('r'));
		$this->get_pass($ft_order_id, $ticket_num);*/
		$this->output->set_status_header(200);
		//header('Last-modified:' .date('r'));
		//$this->output->set_status_header(304);
		$this->output->set_header('Last-Modified: '.date('r'));
		$this->test($serialNo);
	}

	// delete apple pass
	public function delete_pass($version, $devices, $deviceId , $registrations, $passTypeId, $serialNo) {
		$authenticaton_token = end(explode(' ', $this->input->get_request_header('Authorization')));
		if (!$this->passes->is_valid_token($authenticaton_token, $serialNo)) {
			$this->output->set_status_header(401);
			exit;
		}
		$this->passes->delete_pass($deviceId, $serialNo);
	}

	public function log() {
		error_log("Printing error log pass");
		error_log(file_get_contents("php://input"));
	}



function test($serialNo) {// User has filled in the card info, so create the pass now
	
	setlocale(LC_ALL, 'ru_RU.UTF-8');
	$this->load->library('PKPass');
		
	
	
	
			/*$insert_data['serial_number'] = $id;
			$insert_data['authentication_token'] = $this->config->item('authenticationToken', 'passbook');
			$insert_data['pass_type_id'] = $this->config->item('passTypeIdentifier', 'passbook');
			$insert_data['last_update_datetime'] = time();
			
			$this->passes->insert_pass($insert_data);
	*/
	// Create pass
	//$pass = new PKPass\PKPass();
$pass = new PKPass();
	$pass->setCertificate('certificates/passbook.p12'); // Set the path to your Pass Certificate (.p12 file)
	$pass->setCertificatePassword('12shreder34'); // Set password for certificate
	$pass->setWWDRcertPath('certificates/AppleWWDR.pem');
	$pass->setJSON('{ 
	"passTypeIdentifier": "'.$this->config->item('passTypeIdentifier', 'passbook').'",
	"formatVersion": 1,
	"organizationName": "'.$this->config->item('organizationName', 'passbook').'",
	"teamIdentifier": "'.$this->config->item('teamIdentifier', 'passbook').'",
	"webServiceURL" : "'.$this->config->item('webServiceURL', 'passbook').'",
  "authenticationToken" : "'.$this->config->item('authenticationToken', 'passbook').'",
  "barcode" : {
    "altText" : "'.$serialNo.'",
    "message" : "'.$serialNo.'",
    "format" : "PKBarcodeFormatPDF417",
    "messageEncoding" : "iso-8859-1"
  },
	"serialNumber": "'.$serialNo.'",
    "backgroundColor": "rgb(240,240,240)",
	"logoText": "",
	"description": "Demo pass",
	"coupon": {
		"primaryFields" : [
      {
        "key" : "offer",
        "label" : " ",
        "value" : " "
      }
    ],
    "auxiliaryFields" : [
      {
        "key" : "expires",
        "label" : "действителен до",
        "value" : "2013-06-01T10:00-05:00",
        "isRelative" : true,
        "dateStyle" : "PKDateStyleShort"
      }
    ],    "backFields" : [
        
        {
            "key" : "website",
            "label" : "",
            "value" : "http://www.corsocomo.ru"
        },
        {
            "key" : "customer-service",
            "label" : "Горячая линия CORSOCOMO",
            "value" : "8 800 333 03 46"
        },
        {
            "key" : "terms",
            "label" : "Условия предоставления скидки",
            "value" : "С 5 марта по 31 мая в магазинах сети CORSOCOMO проходит специальная акция: при покупке на сумму от 3000 руб. Вы получаете в подарок сертификат на скидку в размере 1000 рублей! Скидка предоставляется при совершении повторной покупки любой пары обуви или сумки из коллекции Весна-Лето 2014 в магазинах CORSOCOMO до 31 мая. Подробности уточняйте в магазинах CORSOCOMO в Вашем городе."
        }
    ]
        
        
    }
    
    }');

    // add files to the PKPass package
    $pass->addFile('assets/img/coupons/test/icon.png');
    $pass->addFile('assets/img/coupons/test/icon@2x.png');
    $pass->addFile('assets/img/coupons/test/logo.png');
	 $pass->addFile('assets/img/coupons/test/logo@2x.png');
    $pass->addFile('assets/img/coupons/test/strip.png');
	$pass->addFile('assets/img/coupons/test/strip@2x.png');

    if(!$pass->create(true)) { // Create and output the PKPass
        echo 'Error: '.$pass->getError();
    }
    exit;
	}

}

