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
require_once(LIMB_DIR . '/class/error/Debug.class.php');

Debug :: addTimingPoint('start');

require_once(LIMB_DIR . '/class/Limb.class.php');
require_once(LIMB_DIR . '/class/system/objects_support.inc.php');
require_once(LIMB_DIR . '/class/file_resolvers/file_resolvers_registry.inc.php');
require_once(LIMB_DIR . '/class/filters/FilterChain.class.php');
require_once(LIMB_DIR . '/class/etc/limb_util.inc.php');
require_once(LIMB_DIR . '/class/etc/MessageBox.class.php');

class LimbApplication
{
  function _loadPackages()
  {
    include_once(LIMB_DIR . '/class/PackagesInfo.class.php');
    $inst =& PackagesInfo :: instance();
    $inst->loadPackages();
  }

  function _createToolkit()
  {
    include_once(LIMB_DIR . '/class/LimbBaseToolkit.class.php');
    return new LimbBaseToolkit();
  }

  function _registerToolkit()
  {
    Limb :: registerToolkit($this->_createToolkit());
  }

  function _registerFilters($filter_chain)
  {
    $filters_dir = LIMB_DIR . '/class/filters/';

    $filter_chain->registerFilter($filters_dir . 'session_startup_filter');
    $filter_chain->registerFilter($filters_dir . 'locale_definition_filter');
    $filter_chain->registerFilter($filters_dir . 'authentication_filter');
    $filter_chain->registerFilter($filters_dir . 'full_page_cache_filter');
    $filter_chain->registerFilter($filters_dir . 'image_cache_filter');
    $filter_chain->registerFilter($filters_dir . 'site_object_controller_filter');
  }

  function _registerFileResolvers()
  {
    $resolvers_dir = LIMB_DIR . '/class/file_resolvers/';

    include_once($resolvers_dir . 'PackageFileResolver.class.php');
    include_once($resolvers_dir . 'CachingFileResolver.class.php');
    include_once($resolvers_dir . 'IniFileResolver.class.php');
    include_once($resolvers_dir . 'StringsFileResolver.class.php');
    include_once($resolvers_dir . 'TemplateFileResolver.class.php');
    include_once($resolvers_dir . 'BehaviourFileResolver.class.php');
    include_once($resolvers_dir . 'DbTableFileResolver.class.php');
    include_once($resolvers_dir . 'DatasourceFileResolver.class.php');
    include_once($resolvers_dir . 'SiteObjectFileResolver.class.php');

    registerFileResolver('ini',                 new CachingFileResolver(new IniFileResolver(new PackageFileResolver())));
    registerFileResolver('strings',             new CachingFileResolver(new StringsFileResolver(new PackageFileResolver())));
    registerFileResolver('template',            new CachingFileResolver(new TemplateFileResolver(new PackageFileResolver())));
    registerFileResolver('behaviour',           new CachingFileResolver(new BehaviourFileResolver(new PackageFileResolver())));
    registerFileResolver('db_table',            new CachingFileResolver(new DbTableFileResolver(new PackageFileResolver())));
    registerFileResolver('datasource',          new CachingFileResolver(new DatasourceFileResolver(new PackageFileResolver())));
    registerFileResolver('site_object',         new CachingFileResolver(new SiteObjectFileResolver(new PackageFileResolver())));
  }

  function run()
  {
    $this->_registerFileResolvers();

    $this->_registerToolkit();

    $this->_loadPackages();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $response =& $toolkit->getResponse();

    $filter_chain = new FilterChain($request, $response);

    $this->_registerFilters($filter_chain);

    $filter_chain->process();

    if(catch('LimbException', $e))
    {
      Debug :: writeException($e);
    }
    elseif(catch('Exception', $e))
    {
      echo  'Unexpected PHP exception in ' . $e->getFile() . ' in line ' . $e->getLine();
      echo  '<br>';
      echo  '<pre>';
      echo    $e->getTraceAsString();
      echo  '</pre>';
      echo  'Report this error to the LIMB developers, please.';
      exit;
    }

    if( $response->getContentType() == 'text/html' &&
        $response->getStatus() == 200)//only 200?
    {
      if (Debug :: isConsoleEnabled())
        $response->append(Debug :: parseHtmlConsole());

      $response->append(MessageBox :: parse());//It definitely should be somewhere else!
    }

    $response->commit();
  }
}

?>