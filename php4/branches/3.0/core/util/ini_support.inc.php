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
require_once(LIMB_DIR . '/core/util/Ini.class.php');
require_once(LIMB_DIR . '/core/file_resolvers/file_resolvers_registry.inc.php');

if(!isRegisteredResolver('ini'))
{
  include_once(LIMB_DIR . '/core/file_resolvers/PackageFileResolver.class.php');
  include_once(LIMB_DIR . '/core/file_resolvers/IniFileResolver.class.php');
  registerFileResolver('ini', new IniFileResolver(new PackageFileResolver()));
}

function getIniOption($file_path, $var_name, $group_name = 'default', $use_cache = null)
{
  $ini =& getIni($file_path, $use_cache);
  return $ini->getOption($var_name, $group_name);
}

function getIni($file_name, $use_cache = null)
{
  if (isset($GLOBALS['testing_ini'][$file_name]))
  {
    $resolved_file = VAR_DIR . '/' . $file_name;
    $use_cache = false;
  }
  else
  {
    $resolver =& Handle :: resolve(getFileResolver('ini'));
    $resolved_file = $resolver->resolve($file_name);
  }

  return new Ini($resolved_file, $use_cache);
}

?>