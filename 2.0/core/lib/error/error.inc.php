<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: error.inc.php 468 2004-02-18 12:03:43Z server $
*
***********************************************************************************/ 

require_once(LIMB_DIR . 'core/lib/debug/debug.class.php');
require_once(LIMB_DIR . 'core/lib/security/user.class.php');
require_once(LIMB_DIR . 'core/lib/system/sys.class.php');
require_once(LIMB_DIR . 'core/lib/mail/mime_mail.class.php');

if(!defined('ERROR_HANDLER_TYPE'))
	debug :: set_handle_type(DEBUG_HANDLE_CUSTOM);
else
	debug :: set_handle_type(ERROR_HANDLER_TYPE);

function error($description, $error_place='', $params=array()) 
{
	if(defined('DEVELOPER_ENVIROMENT'))
	{
		trigger_error('error', E_USER_WARNING);
		
		echo(  $description . '<br>' . $error_place . '<br><pre>');
		print_r($params);
		echo('</pre>');
	}
		
	debug :: write_error($description, $error_place, $params);
	
	rollback_user_transaction();
	
	if (debug :: is_console_enabled())
		echo debug :: parse_html_console();
	else
	{	
		$message = '';
		
		if($user_id = user :: get_id())
			$message .= "user id:\t$user_id\nlogin:\t\t"  . user :: get_login() . "\ne-mail:\t\t" . user :: get_email() . "\n";

		$message .= "ip:\t\t" . sys::client_ip() . "\nrequest:\t" . REQUEST_URI . "\nerror:\t\t$title\ndescription:\t$msg";
				
		$mail = new mime_mail();
		$mail->set_body($message);
		$mail->build_message();
		$mail->send('developer', DEVELOPER_EMAIL, '', WEBSITE_EMAIL, $_SERVER['HTTP_HOST'] . ' internal error!');
	}
	ob_end_flush();
			
	exit;
}
?>