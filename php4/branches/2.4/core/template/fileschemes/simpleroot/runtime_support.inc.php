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
* Determines the full path to a compiled template file.
*/
function resolve_template_compiled_file_name($sourcefile)
{
  if (defined('CONTENT_LOCALE_ID'))
    $locale = '_' . CONTENT_LOCALE_ID . '/';
  else
    $locale = '_' . DEFAULT_CONTENT_LOCALE_ID . '/';

  return VAR_DIR . '/compiled/' . md5($sourcefile . $locale) . '.php';
}

/**
* Returns the contents of a compiled template file
*/
function read_template_file($file)
{
  return file_get_contents($file);
}

?>