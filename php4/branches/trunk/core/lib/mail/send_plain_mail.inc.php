<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

function send_plain_mail($recipients, $sender, $subject, $body)
{
  include_once(PHPMailer_DIR . '/class.phpmailer.php');

  $mail = new PHPMailer();
  $mail->IsHTML(false);

  foreach($recipients as $recipient)
    $mail->AddAddress($recipient);

  $mail->From = $sender;
  $mail->Subject = $subject;
  $mail->Body    = $body;

  return $mail->Send();
}
?>