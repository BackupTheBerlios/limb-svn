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
// Simple renderer for OPTIONs.  Does not support disabled and label attributes.
// Does not support OPTGROUP tags.
/**
* Deals with rendering option elements for HTML select tags
*
*/
class OptionRenderer
{
  /**
  * Renders an option, sending directly to display. Called from a compiled
  * template render function.
  */
  function renderAttribute($key, $contents, $selected)
  {
    echo '<option value="';
    echo htmlspecialchars($key, ENT_QUOTES);
    echo '"';
    if ($selected)
    {
      echo " selected";
    }
    echo '>';
    if (empty($contents))
    {
      echo htmlspecialchars($key, ENT_QUOTES);
    }
    else
    {
      echo $contents;
    }
    echo '</option>';
  }
}

?>