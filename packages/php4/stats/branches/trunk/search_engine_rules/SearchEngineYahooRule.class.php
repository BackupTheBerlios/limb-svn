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
  function SearchEngineYahooRule()
  {
    parent :: SearchEngineRegexRule('yahoo', '/^.*search\.yahoo.*\?p=([^&]*).*$/', 1);
  }

  function getMatchingPhrase()
  {
    include_once(LIMB_DIR . '/core/http/utf8_to_win1251.inc.php');
    return utf8ToWin1251(parent :: getMatchingPhrase());
  }
}

?>