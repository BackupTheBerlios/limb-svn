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
require_once(LIMB_DIR . '/core/lib/i18n/utf8.inc.php');

/**
* Determines the full path to a source template file.
*/
function resolve_template_source_file_name($file)
{
  if (defined('CONTENT_LOCALE_ID'))
    $locale = '_' . CONTENT_LOCALE_ID . '/';
  else
    $locale = '_' . DEFAULT_CONTENT_LOCALE_ID . '/';

  if(file_exists(PROJECT_DIR . '/design/main/templates/' . $locale. $file))	//fix this!!!
    return PROJECT_DIR . '/design/main/templates/' . $locale. $file;

  if(file_exists(PROJECT_DIR . '/design/main/templates/' . $file))
    return PROJECT_DIR . '/design/main/templates/' . $file;

  if(file_exists(LIMB_DIR . '/design/main/templates/' . $locale. $file))
    return LIMB_DIR . '/design/main/templates/' . $locale. $file;

  if(file_exists(LIMB_DIR . '/design/main/templates/' . $file))
    return LIMB_DIR . '/design/main/templates/' . $file;

  if(file_exists(LIMB_DIR . '/design/default/templates/' . $locale. $file))
    return LIMB_DIR . '/design/default/templates/' . $locale. $file;

  if(file_exists(LIMB_DIR . '/design/default/templates/' . $file))
    return LIMB_DIR . '/design/default/templates/' . $file;

  if (file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/templates/' . $locale. $file))
    return dirname($_SERVER['SCRIPT_FILENAME']) . '/templates/' . $locale. $file;

  if (file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/templates/' . $file))
    return dirname($_SERVER['SCRIPT_FILENAME']) . '/templates/' . $file;

  return null;
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
  if (fwrite($fp, $data, strlen($data)))//note not utf8_strlen!!!
  {
    fclose($fp);
  }
}

?>