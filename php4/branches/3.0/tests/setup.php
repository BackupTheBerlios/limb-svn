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
if (file_exists(dirname(__FILE__) . '/setup.override.php'))
  include_once(dirname(__FILE__) . '/setup.override.php');

@define('LIMB_DIR', dirname(__FILE__) . '/../');
@define('VAR_DIR', dirname(__FILE__) . '/var/');
@define('CONTENT_LOCALE_ID', 'en');
@define('MANAGEMENT_LOCALE_ID', 'en');
@define('DEFAULT_MANAGEMENT_LOCALE_ID', 'en');
@define('DEFAULT_CONTENT_LOCALE_ID','en');

//WACT init
@define('WACT_CONFIG_DIRECTORY', dirname(__FILE__) . '/settings/');
@define('WACT_ROOT', dirname(__FILE__) . '/../external/wact/framework/');
require_once(WACT_ROOT . '/common.inc.php');
restore_error_handler();
//@define('TMPL_FILESCHEME_PATH', WACT_ROOT . '/../tests/filescheme/');
@define('TMPL_FILESCHEME_PATH', LIMB_DIR . '/tests/template/fileschemes/');

require_once(LIMB_DIR . '/core/Limb.class.php');
require_once(LIMB_DIR . '/setup.php');
require_once(LIMB_DIR . '/tests/lib/DebugMock.class.php');//don't move this line!!!

require_once(LIMB_DIR . '/core/file_resolvers/file_resolvers_registry.inc.php');
include_once(LIMB_DIR . '/core/file_resolvers/PackageFileResolver.class.php');
include_once(LIMB_DIR . '/core/file_resolvers/DbTableFileResolver.class.php');
include_once(LIMB_DIR . '/core/file_resolvers/ServiceFileResolver.class.php');
include_once(LIMB_DIR . '/core/file_resolvers/DAOFileResolver.class.php');
include_once(LIMB_DIR . '/core/file_resolvers/ObjectFileResolver.class.php');
include_once(LIMB_DIR . '/core/file_resolvers/TemplateFileResolver.class.php');

registerFileResolver('ini',         new LimbHandle(LIMB_DIR . '/tests/lib/TestsIniFileResolver'));
registerFileResolver('action',      new LimbHandle(LIMB_DIR . '/tests/lib/TestsActionFileResolver'));
registerFileResolver('strings',     new LimbHandle(LIMB_DIR . '/tests/lib/TestsStringsFileResolver'));
registerFileResolver('db_table',    new DbTableFileResolver(new PackageFileResolver()));
registerFileResolver('template',    new TemplateFileResolver(new PackageFileResolver()));
registerFileResolver('service',     new ServiceFileResolver(new PackageFileResolver()));
registerFileResolver('dao',         new DAOFileResolver(new PackageFileResolver()));
registerFileResolver('object',      new ObjectFileResolver(new PackageFileResolver()));

require_once(LIMB_DIR . '/tests/lib/test_utils.php');
require_once(LIMB_DIR . '/tests/lib/TestFinder.class.php');
require_once(LIMB_DIR . '/core/error/error.inc.php');
require_once(LIMB_DIR . '/core/PackagesInfo.class.php');
require_once(LIMB_DIR . '/core/db/SimpleDb.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');

require_once(LIMB_DIR . '/tests/setup_SimpleTest.inc.php');
require_once(LIMB_DIR . '/tests/lib/LimbTestCase.class.php');
require_once(LIMB_DIR . '/tests/lib/LimbGroupTest.class.php');

require_once(LIMB_DIR . '/core/exceptions/setup.php');

set_time_limit(0);
error_reporting(E_ALL);

?>