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
require_once(LIMB_DIR . '/class/core/packages_info.class.php');
require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolver.interface.php');

class package_file_resolver implements file_resolver
{
  protected $_packages_info = null;

  function __construct()
  {
    $this->_packages_info = packages_info :: instance();
  }

  public function resolve($file_path, $params = array())
  {
    $packages = $this->_get_packages();

    foreach($packages as $package)
    {
      if (!isset($package['path']))
        continue;

      $resolved_file_path = $package['path'] . $file_path;
      if (file_exists($resolved_file_path))
        return $resolved_file_path;
    }

    throw new FileNotFoundException('file not found in packages', $file_path);
  }

  protected function _get_packages()
  {
    return $this->_packages_info->get_packages();
  }

}

?>