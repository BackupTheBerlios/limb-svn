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
require_once(LIMB_DIR . '/core/PackagesInfo.class.php');

class PackageFileResolver// implements FileResolver
{
  var $_packages_info = null;

  function PackageFileResolver()
  {
    $this->_packages_info =& PackagesInfo :: instance();
  }

  function resolve($file_path, $params = array())
  {
    $packages = $this->_getPackages();

    foreach($packages as $package)
    {
      if (!isset($package['path']))
        continue;

      $resolved_file_path = $package['path'] . $file_path;
      if (file_exists($resolved_file_path))
        return $resolved_file_path;
    }

    return throw_error(new FileNotFoundException('file not found in packages', $file_path));
  }

  function _getPackages()
  {
    return $this->_packages_info->getPackages();
  }

}

?>