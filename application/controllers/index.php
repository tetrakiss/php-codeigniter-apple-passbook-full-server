<?php
if(isset($_POST['name'])){
	// User has filled in the card info, so create the pass now
	
	setlocale(LC_MONETARY, 'en_US');
	require('src/PKPass.php');
		
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
	"passTypeIdentifier": "pass.ru.corsocomo.pass",
	"formatVersion": 1,
	"organizationName": "CORSOCOMO",
	"teamIdentifier": "D38PNYE7U5",
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
	
}else{
	// User lands here, there are no $_POST variables set	
	?>
	<html>
		<head>
			<title>Starbucks pass creator - PHP class demo</title>
			
			<!-- Reusing some CSS from another project of mine -->
			<link href="http://www.lifeschool.nl/static/bootstrap.css" rel="stylesheet" type="text/css" />
			<meta name="viewport" content="width=320; user-scalable=no" />
			<style>
				.header { background-color: #CCC; padding-top: 30px; padding-bottom: 30px; margin-bottom: 32px; text-align: center; }
				.logo { width: 84px; height: 84px; margin-bottom: 20px; }
				.title { color: black; font-size: 22px; text-shadow: 1px 1px 1px rgba(0,0,0,0.1); font-weight: bold; display: block; text-align: center; }
				.userinfo { margin: 0px auto; padding-bottom: 32px; width: 280px;}
				form.form-stacked { padding: 0px;}
				legend { text-align: center; padding-bottom: 25px; border-bottom: none; clear: both;}
				input.xlarge { width: 280px; height: 26px; line-height: 26px;}
			</style>
		</head>
		<body>
			<div class="header">
				<img class="logo" src="logo_web.png" />
				
			</div>
			<div class="userinfo">
				<form action="index.php" method="post" class="form-stacked">
            <fieldset>
                <legend style="padding-left: 0px;">Введите ваши контактные данные</legend>
                                                
                <div class="clearfix">
                	<label style="text-align:left">Ваше имя и фамилия</label>
                	<div class="input">
                		<input class="xlarge" name="name" type="text" value="Иван Иванов" />
                	</div>
                </div>
                 <div class="clearfix">
                	<label style="text-align:left">Ваш телефон</label>
                	<div class="input">
                		<input class="xlarge" name="tel" type="text" value="+7 111 11 11" />
                	</div>
                </div>
                <div class="clearfix">
                	<label style="text-align:left">Ваш email</label>
                	<div class="input">
                		<input class="xlarge" name="tel" type="text" value="ivan@yandex.ru" />
                	</div>
                </div>
                                
                <br /><br />
                <center><input type="submit" class="btn primary" value=" Create pass &gt; " /></center>
            </fieldset>
        </form>

			</div>
		</body>
	</html>
	<?
}