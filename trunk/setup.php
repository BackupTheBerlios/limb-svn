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

if(	preg_match('|^(.*?)\.(.*?)\.(.*?)$|', $_SERVER['SERVER_NAME'], $matches) && 
		$matches[3] == 'bit')
	include_once(LIMB_DIR . 'setup_developer.php');

set_time_limit(300);

if(!defined('DEVELOPER_EMAIL'))
	define('DEVELOPER_EMAIL', 'dbrain@office.bit');
    
define('SHARED_DIR', LIMB_DIR . 'shared/');
define('SHARED_IMG_URL', '/shared/images/');  

define('VAR_DIR', PROJECT_DIR . 'var/');

if(!defined('VAR_URL'))
	define('VAR_URL', '/var/');

if(!defined('CACHE_DIR'))
	define('CACHE_DIR', PROJECT_DIR . 'var/cache/');

if(!defined('MEDIA_DIR')) 	 	
	define('MEDIA_DIR', PROJECT_DIR .'var/media/');

define('MEDIA_SCRIPT', '/media/%s');
define('IMAGE_SCRIPT', '/image/%s.%s');

if (version_compare(phpversion(), '4.2', '<'))
	include_once(LIMB_DIR . '/core/lib/util/php42.php');

if (version_compare(phpversion(), '4.3', '<')) 
	include_once(LIMB_DIR . '/core/lib/util/php43.php');

?>