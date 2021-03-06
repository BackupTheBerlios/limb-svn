<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(WACT_ROOT . '/validation/rule.inc.php');

class UrlRule extends SingleFieldRule
{
  function check($value)
  {
    $regex = "
      \b
      (
        (ftp|https?)://[-\w]+(\.\w[-\w]*)+
        |
        (?i: [a-z0-9] (?:[-a-z0-9]*[-a-z0-9])? \. )+
        (?-i: com\b
            |	edu\b
            |	biz\b
            |	gov\b
            |	in(?:t|fo)\b
            | mil\b
            |	net\b
            |	org\b
            |	[a-z][a-z]\b
        )
      )
      ( : \d+ )?
      (
        /
        [^;\"'<>()\[\]{}\s\x7F-\xFF]*
        (?:
          [..?]+ [^;\"'<>()\[\]{}\s\x7F-\xFF]
        )*
      )?
    ";

    if (!preg_match("~{$regex}~x", $value))
    {
      $this->error('BAD_URL');
    }
  }
}
?>