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
@define('VAR_DIR', LIMB_APP_DIR . 'var/');
@define('MEDIA_DIR', VAR_DIR . 'media/');

if(isset($_SERVER['SERVER_PORT']) &&  $_SERVER['SERVER_PORT'] == 81)
  define('ERROR_HANDLER_TYPE', 'native');

require_once(dirname(__FILE__) . '/core/exceptions/setup.php');

?>