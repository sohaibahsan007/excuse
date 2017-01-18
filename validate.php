<?php 
        
// server response codes
define('LICENSE_VALID',   '601');
define('LICENSE_INVALID', '602');

// database connection parameters
$db_host = 'localhost:3306';
$db_user = 'root';
$db_pass = 'root123';
$db_name = 'mydb';

// client information table
$clients_tbl_name = 'clients';
$sn_tbl_col       = 'serial_no';

function ServerResponse($is_valid, $posted_serial = '', $lang_id = 1033)
{
  $msg_sep = "\n";
  
  // load error messages from your database, using "$lang_id" for localization (optional)
  
  if($posted_serial == '')
    return LICENSE_INVALID . $msg_sep . "Missing Serial Number !";
  
  if($is_valid == true)
    return LICENSE_VALID;
  else
    return LICENSE_INVALID . $msg_sep . "Serial Number: " . $posted_serial . ' is invalid !';  
}

// Variables POSTed by Advanced Installer serial validation tool to this web page: "sn", "languageid".
if(isset($_POST['sn']) && trim($_POST['sn']) != '')
{
  // get the serial number entered by the installing user in the "UserRegistrationDlg" dialog 
  $sn = trim($_POST['sn']);
  
  // get the email entered by the installing user in the "UserRegistrationDlg" dialog 
  $email = $_POST['email'];
  
  // get the system language ID of the user's machine
  // (you can use this parameter to display a localized error message taken from your database)
  $languageid = (int) $_POST['languageid'];
  
  // get the additional information entered by the installing user in the "UserRegistrationDlg" dialog 
  $additional_information = $_POST['ai'];

}
if (sn=="233-421" )
	
  {
    // serial number was found in database => issue SUCCESS response
    echo ServerResponse(true, $sn, $languageid);
    die();
  }
}
else
{
  // issue error response
  echo ServerResponse(false);
  die();
}

?>