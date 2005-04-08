<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: compilersupport.inc.php 1098 2005-02-10 12:06:14Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/file_resolvers/file_resolvers_registry.inc.php');

function ResolveTemplateSourceFileName($file, $operation = TMPL_INCLUDE, $context = NULL)
{
  if (isset($GLOBALS['TestingTemplates'][$file]))
    return $file;

  $resolver =& Handle :: resolve(getFileResolver('template'));

  return $resolver->resolve($file);
}

function writeTemplateFile($file, $data)
{
  if(!is_dir(dirname($file)))
    Fs :: mkdir(dirname($file), 0777, true);

  $fp = fopen($file, "wb");
  if (fwrite($fp, $data, strlen($data)))
    fclose($fp);
}

function CompileEntireFileScheme(){}

?>