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
if(!defined('VAR_DIR'))
  define('VAR_DIR', LIMB_APP_DIR . 'var/');

if(!defined('MEDIA_DIR'))
  define('MEDIA_DIR', VAR_DIR . 'media/');

if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 81)
  define('ERROR_HANDLER_TYPE', 'native');

require_once(dirname(__FILE__) . '/class/exceptions/setup.php');

?>