<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
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

register_file_resolver('ini',    $r = array(LIMB_DIR . '/tests/lib/package_tests_ini_file_resolver', dirname(__FILE__) . '/../'));

?>