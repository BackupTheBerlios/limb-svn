<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: back.php 401 2004-02-04 15:40:14Z server $
*
***********************************************************************************/ 
ob_start();	

include_once('setup.php');	
require_once(LIMB_DIR . 'core/lib/http/control_flow.php');

return_back();

ob_end_flush();
?>