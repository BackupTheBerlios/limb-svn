<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: send_plain_mail.php 62 2004-03-23 15:03:17Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/lib/mail/mime_mail.class.php');

function send_html_mail($recipients, $sender, $subject, $html, $text = null, $headers = array())
{
	$mail = new mime_mail();
	$mail->set_html($html, $text);
	$mail->set_subject($subject);
	$mail->set_from($sender);
	
	foreach($headers as $key => $value)
		$mail->set_header($key, $value);
	
	return $mail->send($recipients);
}
?>