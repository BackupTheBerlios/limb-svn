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
require_once(LIMB_DIR . '/core/model/stats/search_engine_rules/search_engine_regex_rule.class.php');

class search_engine_rambler_rule extends search_engine_regex_rule
{
  function search_engine_rambler_rule()
  {
    parent :: search_engine_regex_rule('rambler', '/^.*rambler.*words=([^&]*).*$/', 1);
  }
}

?>