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

/**
* Creates a new ID for a server component, if one wasn't found. Called from
* compiler_component::get_server_id()
*/
function getNewServerId()
{
  static $server_id_counter = 1;
  return 'id00' . $server_id_counter++;
}

/**
* Adds further quotes to a regex pattern
*/
function pregReplacementQuote($replacement)
{
  $replacement = str_replace("\\", "\\\\", $replacement);
  $replacement = str_replace("$", "\\$", $replacement);
  return $replacement;
}


?>