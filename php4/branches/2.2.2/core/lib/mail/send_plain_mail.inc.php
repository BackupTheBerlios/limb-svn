<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/lib/mail/mime_mail.class.php');

function send_plain_mail($recipients, $sender, $subject, $body, $headers = array())
{
	$mail = new mime_mail();
	$mail->set_text($body);
	$mail->set_subject($subject);
	$mail->set_from($sender);
	
	foreach($headers as $key => $value)
		$mail->set_header($key, $value);
	
	return $mail->send($recipients);
}
?>