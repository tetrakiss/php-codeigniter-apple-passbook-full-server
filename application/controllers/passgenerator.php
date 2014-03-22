<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');


class Passgenerator extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('passbook', 'passbook');
	}
	
function test() {// User has filled in the card info, so create the pass now
	
	setlocale(LC_ALL, 'ru_RU.UTF-8');
	$this->load->library('PKPass');
		
	// Variables
	$id = rand(100000,999999) . '-' . rand(100,999) . '-' . rand(100,999); // Every card should have a unique serialNumber
	$balance = '$'.rand(0,30).'.'.rand(10,99); // Create random balance
	$name = stripslashes($_POST['name']);
	
	
	// Create pass
	$pass = new PKPass\PKPass();

	$pass->setCertificate('passbook.p12'); // Set the path to your Pass Certificate (.p12 file)
	$pass->setCertificatePassword('12shreder34'); // Set password for certificate
	$pass->setWWDRcertPath('AppleWWDR.pem');
	$pass->setJSON('{ 
	"passTypeIdentifier": " ",
	"formatVersion": 1,
	"organizationName": " ",
	"teamIdentifier": " ",
	"webServiceURL" : "https://passbook.corso-como.ru",
  "authenticationToken" : "vxwxd7J8AlNNFPS8k0a0FfUFtq0ewzFdc",
  "barcode" : {
    "altText" : "'.$id.'",
    "message" : "'.$id.'",
    "format" : "PKBarcodeFormatPDF417",
    "messageEncoding" : "iso-8859-1"
  },
	"serialNumber": "'.$id.'",
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
        "value" : "2013-05-31T10:00-05:00",
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
    $pass->addFile('icon.png');
    $pass->addFile('icon@2x.png');
    $pass->addFile('logo.png');
	 $pass->addFile('logo@2x.png');
    $pass->addFile('strip.png');
	$pass->addFile('strip@2x.png');

    if(!$pass->create(true)) { // Create and output the PKPass
        echo 'Error: '.$pass->getError();
    }
    exit;
	}
}