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

class SearchEngineAportRule extends SearchEngineRegexRule
{
  function SearchEngineAportRule()
  {
    parent :: SearchEngineRegexRule('aport', '/^.*sm\.aport.*r=([^&]*).*$/', 1);
  }
}

?>