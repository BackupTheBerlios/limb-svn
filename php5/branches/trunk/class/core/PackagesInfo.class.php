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
  protected static $_instance = null;

  protected $_packages = array();

  static public function instance()
  {
    if (!self :: $_instance)
      self :: $_instance = new PackagesInfo();

    return self :: $_instance;
  }

  public function reset()
  {
    $this->_packages = array();
  }

  public function getPackages()
  {
    if(!$this->_packages)
      $this->loadPackages();

    return $this->_packages;
  }

  public function loadPackages()
  {
    include_once(LIMB_DIR . '/class/lib/util/ini_support.inc.php');

    $ini = Limb :: toolkit()->getINI('packages.ini');
    $this->_packages = array();

    $groups = $ini->getAll();

    $packages = $ini->getOption('packages');

    if (!count($packages))
      throw new LimbException('no packages in package.ini!');

    foreach($packages as $package_path)
    {
      $package_data = array();

      $package_path = $this->_parsePath($package_path);

      include($package_path . '/setup.php');

      $this->_definePackageConstant($PACKAGE_NAME, $package_path);

      $package_data['path'] = $package_path;
      $package_data['name'] = $PACKAGE_NAME;

      $this->_packages[] = $package_data;
    }
  }

  protected function _definePackageConstant($package_name, $path)
  {
    if(!defined($package_name . '_DIR'))
      define($package_name . '_DIR', $path);
  }

  protected function _parsePath($path)
  {
    return preg_replace('~\{([^\}]+)\}~e', "constant('\\1')", $path);
  }

}

?>