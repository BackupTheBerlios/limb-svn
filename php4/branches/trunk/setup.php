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
if(!defined('AVAILABLE_LOCALES'))
  define('AVAILABLE_LOCALES', 'en');

$AVAILABLE_LOCALES = explode(',', AVAILABLE_LOCALES);//!!!refactor

@define('DEVELOPER_EMAIL', 'developer@mail.com');

@define('SHARED_DIR', LIMB_DIR . '/shared/');
@define('SHARED_IMG_URL', '/shared/images/');

@define('VAR_DIR', PROJECT_DIR . '/var/');

@define('VAR_WEB_DIR', '/var/');

@define('CACHE_DIR', PROJECT_DIR . '/var/cache/');

@define('MEDIA_DIR', PROJECT_DIR .'/media/');

if (version_compare(phpversion(), '4.2', '<'))
  include_once(LIMB_DIR . '/core/lib/util/php42.php');

if (version_compare(phpversion(), '4.3', '<'))
  include_once(LIMB_DIR . '/core/lib/util/php43.php');

?>