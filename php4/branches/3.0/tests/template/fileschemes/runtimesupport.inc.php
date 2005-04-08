<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: runtimesupport.inc.php 1013 2005-01-12 12:13:22Z pachanga $
*
***********************************************************************************/

$GLOBALS['TestingTemplates'] = array();

/**
* Determines the full path to a compiled template file.
*/
function ResolveTemplateCompiledFileName($file, $operation = TMPL_INCLUDE)
{
  if (defined('CONTENT_LOCALE_ID'))
    $locale = '_' . CONTENT_LOCALE_ID . '/';
  elseif(defined('DEFAULT_CONTENT_LOCALE_ID'))
    $locale = '_' . DEFAULT_CONTENT_LOCALE_ID . '/';
  else
    $locale = '';

  if(isset($GLOBALS['TestingTemplates'][$file]))
    return VAR_DIR . '/compiled/' . $file . '.php';
  else
    return VAR_DIR . '/compiled/' . md5($file . $locale) . '.php';
}

/**
* Returns the contents of a compiled template file
*/
function readTemplateFile($file)
{
  if (isset($GLOBALS['TestingTemplates'][$file]))
    return $GLOBALS['TestingTemplates'][$file];

  return file_get_contents($file);
}

function RegisterTestingTemplate($file, $template)
{
  if (isset($GLOBALS['TestingTemplates'][$file]))
  {
    die("Duplicate template registration not allowed.");
  }

  $GLOBALS['TestingTemplates'][$file] = $template;
}

function ClearTestingTemplates()
{
  if(!isset($GLOBALS['TestingTemplates']) || !sizeof($GLOBALS['TestingTemplates']))
    return;

  foreach(array_keys($GLOBALS['TestingTemplates']) as $file)
  {
    $compiled = VAR_DIR . '/compiled/' . $file . '.php';
    if(file_exists($compiled))
    {
      unlink($compiled);
    }
  }
  clearstatcache();

  $GLOBALS['TestingTemplates'] = array();
}


?>