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

class SearchEngineYahooRule extends SearchEngineRegexRule
{
  public function __construct()
  {
    parent :: __construct('yahoo', '/^.*search\.yahoo.*\?p=([^&]*).*$/', 1);
  }

  public function getMatchingPhrase()
  {
    return utf8ToWin1251(parent :: getMatchingPhrase());
  }
}

?>