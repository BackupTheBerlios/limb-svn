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
if (file_exists(dirname(__FILE__) . '/setup.override.php'))
  include_once(dirname(__FILE__) . '/setup.override.php');

require_once(LIMB_DIR . '/tests/setup.php');
require_once(LIMB_DIR . '/class/core/PackagesInfo.class.php');

registerFileResolver('ini', $r = array(LIMB_DIR . '/tests/lib/PackageTestsIniFileResolver', dirname(__FILE__) . '/../'));

$info =& PackagesInfo :: instance();
$info->loadPackages();

?>