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
require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolvers_registry.inc.php');

/**
* Determines the full path to a source template file.
*/
function resolve_template_source_file_name($file)
{
  resolve_handle($resolver =& get_file_resolver('template'));

  return $resolver->resolve($file);
}

/**
* Writes a compiled template file
*
* @param string $ filename
* @param string $ content to write to the file
* @return void
* @access protected
*/
function write_template_file($file, $data)
{
  if(!is_dir(dirname($file)))
    fs :: mkdir(dirname($file), 0777, true);

  $fp = fopen($file, "wb");
  if (fwrite($fp, $data, strlen($data)))
  {
    fclose($fp);
  }
}

?>