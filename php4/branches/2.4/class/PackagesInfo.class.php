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
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');

class PackagesInfo
{
  var $_packages = array();

  function & instance()
  {
    if (!isset($GLOBALS['PackagesInfo']) || !is_a($GLOBALS['PackagesInfo'], 'PackagesInfo'))
      $GLOBALS['PackagesInfo'] =& new PackagesInfo();

    return $GLOBALS['PackagesInfo'];
  }

  function reset()
  {
    $this->_packages = array();
  }

  function getPackages()
  {
    if(!$this->_packages)
      $this->loadPackages();

    return $this->_packages;
  }

  function loadPackages()
  {
    include_once(LIMB_DIR . '/class/lib/util/ini_support.inc.php');

    $toolkit =& Limb :: toolkit();
    $ini =& $toolkit->getINI('packages.ini');
    $this->_packages = array();

    $groups = $ini->getAll();

    $packages = $ini->getOption('packages');

    if (!count($packages))
      return throw(new LimbException('no packages in package.ini!'));

    foreach($packages as $package_path)
    {
      $package_data = array();

      include($package_path . '/setup.php');

      $this->_definePackageConstant($PACKAGE_NAME, $package_path);

      $package_data['path'] = $package_path;
      $package_data['name'] = $PACKAGE_NAME;

      $this->_packages[] = $package_data;
    }
  }

  function _definePackageConstant($package_name, $path)
  {
    @define($package_name . '_DIR', $path);
  }
}

?>