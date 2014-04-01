<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
To find your Team ID, do the following:

Open Keychain Access and select your certificate.
Select File > Get Info and find the Organizational Unit section under Details. This is your Team ID.
The pass type identifier appears in the certificate under the User ID section.

*/

$config['passTypeIdentifier'] = 'pass.com.youdomain';

$config['teamIdentifier'] = 'AAAAAAAAAA';
$config['organizationName'] = 'youdomain';

/*
A REST-style web service protocol is used to communicate with your server about changes to passes, and to fetch the latest version of a pass when it has changed.

for test use http for prodaction use only HTTPS without Self sign certificate
*/

$config['webServiceURL'] = 'http://youdomain.com/passbook/connect/';

/*
The authentication token is a shared secret between the user’s device and your server. It shows that the request for an update to a pass is actually coming from the user who has the pass, and not from a third party.

Some random letters and digs

*/

$config['authenticationToken'] = 'vxwxd7J8AlNNFPS8k0a0FfUFtq0ewzFdc';




