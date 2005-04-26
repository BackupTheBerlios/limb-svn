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
require_once(LIMB_DIR . '/core/error/Debug.class.php');

Debug :: addTimingPoint('start');

require_once(LIMB_DIR . '/core/Limb.class.php');
require_once(LIMB_DIR . '/core/file_resolvers/file_resolvers_registry.inc.php');
require_once(LIMB_DIR . '/core/filters/FilterChain.class.php');
require_once(LIMB_DIR . '/core/etc/limb_util.inc.php');
require_once(LIMB_DIR . '/core/etc/MessageBox.class.php');

class LimbApplication
{
  function _loadPackages()
  {
    include_once(LIMB_DIR . '/core/PackagesInfo.class.php');
    $inst =& PackagesInfo :: instance();
    $inst->loadPackages();
  }

  function _createToolkit()
  {
    include_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
    return new LimbBaseToolkit();
  }

  function _registerToolkit()
  {
    Limb :: registerToolkit($this->_createToolkit());
  }

  function _registerFilters($filter_chain)
  {
    die('abstract method!');
  }

  function _registerFileResolvers()
  {
    $resolvers_dir = LIMB_DIR . '/core/file_resolvers/';

    include_once($resolvers_dir . 'PackageFileResolver.class.php');
    include_once($resolvers_dir . 'CachingFileResolver.class.php');
    include_once($resolvers_dir . 'IniFileResolver.class.php');
    include_once($resolvers_dir . 'StringsFileResolver.class.php');
    include_once($resolvers_dir . 'TemplateFileResolver.class.php');
    include_once($resolvers_dir . 'ServiceFileResolver.class.php');
    include_once($resolvers_dir . 'DbTableFileResolver.class.php');
    include_once($resolvers_dir . 'DAOFileResolver.class.php');
    include_once($resolvers_dir . 'ObjectFileResolver.class.php');

    registerFileResolver('ini',                 new CachingFileResolver(new IniFileResolver(new PackageFileResolver())));
    registerFileResolver('strings',             new CachingFileResolver(new StringsFileResolver(new PackageFileResolver())));
    registerFileResolver('template',            new CachingFileResolver(new TemplateFileResolver(new PackageFileResolver())));
    registerFileResolver('service',           new CachingFileResolver(new ServiceFileResolver(new PackageFileResolver())));
    registerFileResolver('db_table',            new CachingFileResolver(new DbTableFileResolver(new PackageFileResolver())));
    registerFileResolver('dao',                 new CachingFileResolver(new DAOFileResolver(new PackageFileResolver())));
    registerFileResolver('object',              new CachingFileResolver(new ObjectFileResolver(new PackageFileResolver())));
  }

  function run()
  {
    $this->_registerFileResolvers();

    $this->_registerToolkit();

    $this->_loadPackages();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $response =& $toolkit->getResponse();
    $context = new DataSpace();

    $filter_chain = new FilterChain($request, $response, $context);

    $this->_registerFilters($filter_chain);

    $filter_chain->process();

    if(catch_error('LimbException', $e))
    {
      Debug :: writeException($e);
    }
    elseif(catch_error('LimbException', $e))
    {
      echo  'Unexpected PHP exception in ' . $e->getFile() . ' in line ' . $e->getLine();
      echo  '<br>';
      echo  '<pre>';
      echo    $e->getTraceAsString();
      echo  '</pre>';
      echo  'Report this error to the LIMB developers, please.';
      exit();
    }
  }
}

?>