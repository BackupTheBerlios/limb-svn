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

@define('LIMB_DIR', dirname(__FILE__) . '/../');
@define('VAR_DIR', dirname(__FILE__) . '/var/');
@define('CONTENT_LOCALE_ID', 'en');
@define('MANAGEMENT_LOCALE_ID', 'en');
@define('DEFAULT_MANAGEMENT_LOCALE_ID', 'en');
@define('DEFAULT_CONTENT_LOCALE_ID','en');

require_once(LIMB_DIR . '/setup.php');
require_once(LIMB_DIR . '/tests/lib/DebugMock.class.php');//don't move this line!!!

require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolvers_registry.inc.php');
include_once(LIMB_DIR . '/class/core/file_resolvers/PackageFileResolver.class.php');
include_once(LIMB_DIR . '/class/core/file_resolvers/DbTableFileResolver.class.php');
include_once(LIMB_DIR . '/class/core/file_resolvers/BehaviourFileResolver.class.php');
include_once(LIMB_DIR . '/class/core/file_resolvers/DatasourceFileResolver.class.php');
include_once(LIMB_DIR . '/class/core/file_resolvers/SiteObjectFileResolver.class.php');
include_once(LIMB_DIR . '/class/core/file_resolvers/TemplateFileResolver.class.php');

registerFileResolver('ini',         LIMB_DIR . '/tests/lib/testsIniFileResolver');
registerFileResolver('action',      LIMB_DIR . '/tests/lib/testsActionFileResolver');
registerFileResolver('strings',     LIMB_DIR . '/tests/lib/testsStringsFileResolver');
registerFileResolver('db_table',    new DbTableFileResolver(new PackageFileResolver()));
registerFileResolver('template',    new TemplateFileResolver(new PackageFileResolver()));
registerFileResolver('behaviour',   new BehaviourFileResolver(new PackageFileResolver()));
registerFileResolver('datasource',  new DatasourceFileResolver(new PackageFileResolver()));
registerFileResolver('site_object', new SiteObjectFileResolver(new PackageFileResolver()));

require_once(LIMB_DIR . '/tests/setup_SimpleTest.inc.php');
require_once(LIMB_DIR . '/tests/lib/test_utils.php');
require_once(LIMB_DIR . '/tests/cases/LimbTestCase.class.php');
require_once(LIMB_DIR . '/tests/lib/TestFinder.class.php');
require_once(LIMB_DIR . '/class/lib/error/error.inc.php');
require_once(LIMB_DIR . '/class/core/PackagesInfo.class.php');
require_once(LIMB_DIR . '/class/core/Limb.class.php');
require_once(LIMB_DIR . '/class/core/BaseLimbToolkit.class.php');

Limb :: registerToolkit(new BaseLimbToolkit());

$inst =& PackagesInfo :: instance();
$inst->loadPackages();//???

set_time_limit(0);
error_reporting(E_ALL);

?>