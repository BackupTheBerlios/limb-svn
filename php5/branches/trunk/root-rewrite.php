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
if (file_exists('protect.php'))
  include_once('protect.php');

require_once('setup.php');

require(LIMB_DIR.'/mod_rewrite_fix.php');
require(LIMB_DIR . '/root.php');
?>