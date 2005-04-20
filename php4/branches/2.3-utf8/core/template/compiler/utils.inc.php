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

/**
* Creates a new ID for a server component, if one wasn't found. Called from
* compiler_component::get_server_id()
*/
function get_new_server_id()
{
  static $server_idCounter = 1;
  return 'id00' . $server_idCounter++;
}

/**
* Adds further quotes to a regex pattern
*/
function preg_replacement_quote($replacement)
{
  $replacement = utf8_str_replace("\\", "\\\\", $replacement);
  $replacement = utf8_str_replace("$", "\\$", $replacement);
  return $replacement;
}

/**
* Debugging method to dump the component tree below the supplied component
* to screen
*/
function dump_root_compiler_component(&$component)
{
  if ($component)
  {
    echo get_class($component) . ' (' . $component->get_server_id() . ")<BR>\n";
    if (count($component->children) > 0)
    {
      echo "<BLOCKQUOTE>\n";
      echo "<HR>\n";
      echo htmlspecialchars($component->contents);
      echo "<HR>\n";
      foreach(array_keys($component->children) as $key)
      {
        dump_root_compiler_component($component->children[$key]);
      }
      echo "</BLOCKQUOTE>\n";
    }
  }
}

?>