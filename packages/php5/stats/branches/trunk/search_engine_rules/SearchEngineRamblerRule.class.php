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

class SearchEngineRamblerRule extends SearchEngineRegexRule
{
  public function __construct()
  {
    parent :: __construct('rambler', '/^.*rambler.*words=([^&]*).*$/', 1);
  }
}

?>