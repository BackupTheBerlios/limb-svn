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
require_once(LIMB_DIR . '/class/lib/error/debug.class.php');

debug :: add_timing_point('start');
  
require_once(LIMB_DIR . '/class/core/limb.class.php');
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolvers_registry.inc.php');
require_once(LIMB_DIR . '/class/core/filters/filter_chain.class.php');
require_once(LIMB_DIR . '/class/etc/limb_util.inc.php');
require_once(LIMB_DIR . '/class/etc/message_box.class.php');

class limb_application
{      
  protected function _load_packages()
  {
    include_once(LIMB_DIR . '/class/core/packages_info.class.php');
    packages_info :: instance()->load_packages();
  }
  
  protected function _create_toolkit()
  {
    include_once(LIMB_DIR . '/class/core/base_limb_toolkit.class.php');
    return new BaseLimbToolkit();
  }
  
  protected function _register_toolkit()
  {
    Limb :: registerToolkit($this->_create_toolkit());
  }
  
  protected function _register_filters($filter_chain)
  {
    $filters_dir = LIMB_DIR . '/class/core/filters/';
    
    $filter_chain->register_filter($filters_dir . 'output_buffering_filter');    
    $filter_chain->register_filter($filters_dir . 'session_startup_filter');
    $filter_chain->register_filter($filters_dir . 'locale_definition_filter');
    $filter_chain->register_filter($filters_dir . 'authentication_filter');
    $filter_chain->register_filter($filters_dir . 'full_page_cache_filter');
    $filter_chain->register_filter($filters_dir . 'jip_filter');
    $filter_chain->register_filter($filters_dir . 'image_cache_filter');
    $filter_chain->register_filter($filters_dir . 'site_object_controller_filter');
  }
  
  protected function _register_file_resolvers()
  {
    $resolvers_dir = LIMB_DIR . '/class/core/file_resolvers/';
    
    include_once($resolvers_dir . 'package_file_resolver.class.php');
    include_once($resolvers_dir . 'caching_file_resolver.class.php');
    include_once($resolvers_dir . 'ini_file_resolver.class.php');
    include_once($resolvers_dir . 'action_file_resolver.class.php');
    include_once($resolvers_dir . 'strings_file_resolver.class.php');
    include_once($resolvers_dir . 'template_file_resolver.class.php');
    include_once($resolvers_dir . 'controller_file_resolver.class.php');
    include_once($resolvers_dir . 'db_table_file_resolver.class.php');
    include_once($resolvers_dir . 'datasource_file_resolver.class.php');
    include_once($resolvers_dir . 'site_object_file_resolver.class.php');
  
    register_file_resolver('ini',                 new caching_file_resolver(new ini_file_resolver(new package_file_resolver())));
    register_file_resolver('action',              new caching_file_resolver(new action_file_resolver(new package_file_resolver())));
    register_file_resolver('strings',             new caching_file_resolver(new strings_file_resolver(new package_file_resolver())));
    register_file_resolver('template',            new caching_file_resolver(new template_file_resolver(new package_file_resolver())));
    register_file_resolver('controller',          new caching_file_resolver(new controller_file_resolver(new package_file_resolver())));
    register_file_resolver('db_table',            new caching_file_resolver(new db_table_file_resolver(new package_file_resolver())));
    register_file_resolver('datasource',          new caching_file_resolver(new datasource_file_resolver(new package_file_resolver())));
    register_file_resolver('site_object',         new caching_file_resolver(new site_object_file_resolver(new package_file_resolver())));
  }
  
  public function run()
  {
    try
    {
      $this->_doRun();
    }
    catch(LimbException $e)
    {
      debug :: write_exception($e);
    }
    catch(Exception $e)
    {
      echo  'Unexpected PHP exception in ' . $e->getFile() . ' in line ' . $e->getLine();
      echo  '<br>';
      echo  '<pre>';
      echo    $e->getTraceAsString();
      echo  '</pre>';
      echo  'Report this error to the LIMB developers, please.';
      exit;
    }
  }
  
  protected function _doRun()
  {
    $this->_register_file_resolvers();
    
    $this->_register_toolkit();
    
    $this->_load_packages();
    
    $request = Limb :: toolkit()->getRequest();
    $response = Limb :: toolkit()->getResponse();
     
    $filter_chain = new filter_chain($request, $response);
    
    $this->_register_filters($filter_chain);
    
    $filter_chain->process();
    
    if( $response->get_content_type() == 'text/html' && 
        $response->get_status() == 200)//only 200?
    {
      if (debug :: is_console_enabled())
      	$response->write(debug :: parse_html_console());
      	
      $response->write(message_box :: parse());//It definitely should be somewhere else!
    }
    
    $response->commit();  
  }
}

?>