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
require_once(dirname(__FILE__) . '/SearchEngineRegexRule.class.php');
require_once(LIMB_DIR . '/class/lib/http/utf8_to_win1251.inc.php');

class SearchEngineGoogleRule extends SearchEngineRegexRule
{
  public function __construct()
  {
    parent :: __construct('google', '/^.*google\..*?q=(cache:[^\s]*\s)?([^&]*).*$/', 2);
  }

  public function getMatchingPhrase()
  {
    return utf8ToWin1251(parent :: getMatchingPhrase());
  }
}

?>