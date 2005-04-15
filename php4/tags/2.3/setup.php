<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
@define('AVAILABLE_LOCALES', 'en');

$AVAILABLE_LOCALES = explode(',', AVAILABLE_LOCALES);//!!!refactor

//making bullet proof settings
if(isset($_SERVER['REQUEST_URI']))
{
  $url = parse_url($_SERVER['REQUEST_URI']);
  $_SERVER['QUERY_STRING'] = isset($url['query']) ? $url['query'] : '';
  $_SERVER['PHP_SELF'] = $url['path'];
}

@define('ADMINISTRATOR_EMAIL', 'admin@dot.com');
@define('DEVELOPER_EMAIL', 'developer@dot.com');

@define('SHARED_DIR', LIMB_DIR . '/shared/');
@define('SHARED_IMG_URL', '/shared/images/');
@define('VAR_DIR', PROJECT_DIR . '/var/');
@define('VAR_WEB_DIR', '/var/');
@define('CACHE_DIR', PROJECT_DIR . '/var/cache/');
@define('MEDIA_DIR', PROJECT_DIR . '/media/');
@define('TEMPLATE_EDITOR_PATH', 'uedit32.exe %s');

//pretty important external dependencies
@define('XML_HTMLSAX3', dirname(__FILE__) . '/../external/XML_HTMLSax3/');
@define('PHPMailer_DIR', dirname(__FILE__) . '/../external/phpmailer/');

if (version_compare(phpversion(), '4.2', '<'))
  include_once(LIMB_DIR . '/core/lib/util/php42.php');

if (version_compare(phpversion(), '4.3', '<'))
  include_once(LIMB_DIR . '/core/lib/util/php43.php');

?>