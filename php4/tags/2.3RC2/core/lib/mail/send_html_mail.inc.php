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
require_once(LIMB_DIR . '/core/lib/mail/mime_mail.class.php');

function send_html_mail($recipients, $sender, $subject, $html, $text = null, $headers = array())
{
  $mail = new mime_mail();

  //$text = convert_html_to_plain_text(preg_replace('(<p>|br>)', "\n", $html));

  $mail->set_html($html, $text);
  $mail->set_subject($subject);
  $mail->set_from($sender);

  foreach($headers as $key => $value)
    $mail->set_header($key, $value);

  return $mail->send($recipients);
}


function convert_html_to_plain_text($html)
{
  $search = array ("'<script[^>]*?>.*?</script>'si",  // Strip out javascript
                 "'<[\/\!]*?[^<>]*?>'si",           // Strip out html tags
                 "'([\r\n])[\s]+'",                 // Strip out white space
                 "'&(quot|#34);'i",                 // Replace html entities
                 "'&(amp|#38);'i",
                 "'&(lt|#60);'i",
                 "'&(gt|#62);'i",
                 "'&(nbsp|#160);'i",
                 "'&(iexcl|#161);'i",
                 "'&(cent|#162);'i",
                 "'&(pound|#163);'i",
                 "'&(copy|#169);'i",
                 "'&#(\d+);'e");                    // evaluate as php

  $replace = array ("",
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