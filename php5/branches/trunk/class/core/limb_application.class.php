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
require_once(LIMB_DIR . 'class/lib/error/debug.class.php');

debug :: add_timing_point('start');

require_once(LIMB_DIR . 'class/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . 'class/core/packages_info.class.php');
require_once(LIMB_DIR . 'class/core/file_resolvers/file_resolvers_registry.inc.php');
require_once(LIMB_DIR . 'class/core/filters/filter_chain.class.php');
require_once(LIMB_DIR . 'class/core/request/http_response.class.php');
require_once(LIMB_DIR . 'class/core/request/request.class.php');
require_once(LIMB_DIR . 'class/etc/limb_util.inc.php');
require_once(LIMB_DIR . 'class/etc/message_box.class.php');

class limb_application
{
  protected $request;
  protected $response;
    
  public function __construct()
  {
    $this->request = request :: instance();
    $this->response = new http_response();  
  }
  
  protected function _load_packages()
  {
    packages_info :: instance()->load_packages();
  }
  
  protected function _register_filters($filter_chain)
  {
    $filter_chain->register_filter(LIMB_DIR . 'class/core/filters/output_buffering_filter');    
    $filter_chain->register_filter(LIMB_DIR . 'class/core/filters/session_startup_filter');
    $filter_chain->register_filter(LIMB_DIR . 'class/core/filters/locale_definition_filter');
    $filter_chain->register_filter(LIMB_DIR . 'class/core/filters/authentication_filter');
    $filter_chain->register_filter(LIMB_DIR . 'class/core/filters/full_page_cache_filter');
    $filter_chain->register_filter(LIMB_DIR . 'class/core/filters/jip_filter');
    $filter_chain->register_filter(LIMB_DIR . 'class/core/filters/image_cache_filter');
    $filter_chain->register_filter(LIMB_DIR . 'class/core/filters/site_object_controller_filter');
  }
  
  protected function _register_file_resolvers()
  {
    //we could make them handles, yet the readability would suffer :(
    include_once(LIMB_DIR . '/class/core/file_resolvers/package_file_resolver.class.php');
    include_once(LIMB_DIR . '/class/core/file_resolvers/caching_file_resolver.class.php');
    include_once(LIMB_DIR . '/class/core/file_resolvers/ini_file_resolver.class.php');
    include_once(LIMB_DIR . '/class/core/file_resolvers/action_file_resolver.class.php');
    include_once(LIMB_DIR . '/class/core/file_resolvers/strings_file_resolver.class.php');
    include_once(LIMB_DIR . '/class/core/file_resolvers/template_file_resolver.class.php');
    include_once(LIMB_DIR . '/class/core/file_resolvers/controller_file_resolver.class.php');
    include_once(LIMB_DIR . '/class/core/file_resolvers/db_table_file_resolver.class.php');
    include_once(LIMB_DIR . '/class/core/file_resolvers/datasource_file_resolver.class.php');
    include_once(LIMB_DIR . '/class/core/file_resolvers/site_object_file_resolver.class.php');
  
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
      $this->_do_run();
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
  
  protected function _do_run()
  {
    $this->_register_file_resolvers();
    
    $this->_load_packages();
    
    $filter_chain = new filter_chain($this->request, $this->response);
    
    $this->_register_filters($filter_chain);
    
    $filter_chain->process();
    
    if( $this->response->get_content_type() == 'text/html' && 
        $this->response->get_status() == 200)//only 200?
    {
      if (debug :: is_console_enabled())
      	$this->response->write(debug :: parse_html_console());
      	
      $this->response->write(message_box :: parse());//It definetly should be somewhere else!
    }
        
    $this->response->commit();  
  }
}

?>