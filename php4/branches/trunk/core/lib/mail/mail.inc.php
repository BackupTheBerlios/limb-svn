<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: mail.inc.php 961 2004-12-15 11:14:32Z pachanga $
*
***********************************************************************************/

function send_plain_mail($recipients, $sender, $subject, $body, $charset = 'windows-1251')
{
  include_once(PHPMailer_DIR . '/class.phpmailer.php');

  $mail = new PHPMailer();
  $mail->IsHTML(false);
  $mail->LE = "\r\n";//we're using php mail function!!!
  $mail->CharSet = $charset;

  $recipients = process_mail_recipients($recipients);

  foreach($recipients as $recipient)
    $mail->AddAddress($recipient['address'], $recipient['name']);

  if(!$sender = process_mail_addressee($sender))
    return false;

  $mail->From = $sender['address'];
  $mail->FromName = $sender['name'];
  $mail->Subject = $subject;
  $mail->Body    = $body;

  return $mail->Send();
}

function send_html_mail($recipients, $sender, $subject, $html, $text = null, $charset = 'windows-1251')
{
  include_once(PHPMailer_DIR . '/class.phpmailer.php');

  $mail = new PHPMailer();
  $mail->IsHTML(true);
  $mail->LE = "\r\n";//we're using php mail function!!!
  $mail->CharSet = $charset;

  $mail->Body = $html;

  if(!is_null($text))
    $mail->AltBody = $text;

  $recipients = process_mail_recipients($recipients);

  foreach($recipients as $recipient)
    $mail->AddAddress($recipient['address'], $recipient['name']);

  if(!$sender = process_mail_addressee($sender))
    return false;

  $mail->From = $sender['address'];
  $mail->FromName = $sender['name'];
  $mail->Subject = $subject;

//  $mail->isSMTP();
  return $mail->Send();
}

function process_mail_recipients($recipients)
{
  if(!is_array($recipients))
     $recipients = array($recipients);
  $result = array();
  foreach($recipients as $recipient)
  {
    if($recipient = process_mail_addressee($recipient))
      $result[] = $recipient;
  }

  return $result;
}

function process_mail_addressee($adressee)
{
  if(is_array($adressee))
  {
    if(isset($adressee['address']) && isset($adressee['name']))
      return $adressee;

    return null;
  }
  elseif(preg_match('~("|\')?([^"\']+)("|\')?\s+<([^>]+)>~', $adressee, $matches))
    return array('address' => $matches[4], 'name' => $matches[2]);
  else
    return array('address' => $adressee, 'name' => '');
}

function convert_html_mail_to_plain_text($html)
{
  $search = array ("'<script[^>]*?>.*?</script>'si",  // Strip out javascript
                 "'<[\/\!]*?[^<>]*?>'si",             // Strip out html tags
                 "'([\r\n])[\s]+'",                   // Strip out white space
                 "'&(quot|#34);'i",                   // Replace html entities
                 "'&(amp|#38);'i",
                 "'&(lt|#60);'i",
                 "'&(gt|#62);'i",
                 "'&(nbsp|#160);'i",
                 "'&(iexcl|#161);'i",
                 "'&(cent|#162);'i",
                 "'&(pound|#163);'i",
                 "'&(copy|#169);'i",
                 "'&#(\d+);'e");                    // evaluate as php

  $replace = array("",
                  "",
                  "\\1",
                  "\"",
                  "&",
                  "<",
                  ">",
                  " ",
                  chr(161),
                  chr(162),
                  chr(163),
                  chr(169),
                  "chr(\\1)");

  return preg_replace ($search, $replace, $html);
}

?>