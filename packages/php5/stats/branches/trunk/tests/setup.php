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
if (file_exists(dirname(__FILE__) . '/constants.php'))
  include_once(dirname(__FILE__) . '/constants.php');

require_once(LIMB_DIR . '/tests/setup.php');

registerFileResolver('ini',    $r = array(LIMB_DIR . '/tests/lib/PackageTestsIniFileResolver',
                                          dirname(__FILE__) . '/../'));

?>