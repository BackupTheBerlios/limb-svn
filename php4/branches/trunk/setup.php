<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
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

if (version_compare(phpversion(), '4.2', '<'))
	include_once(LIMB_DIR . '/class/lib/util/php42.php');

if (version_compare(phpversion(), '4.3', '<')) 
	include_once(LIMB_DIR . '/class/lib/util/php43.php');

if($_SERVER['SERVER_PORT'] == 81)
	define('ERROR_HANDLER_TYPE', 'native');	
	
?>