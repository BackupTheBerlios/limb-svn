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
  private $request;
  private $response;
    
  public function limb_application()
  {
    $this->request = request :: instance();
    $this->response = new http_response();  
  }
  
  protected function _load_packages()
  {
    $p =& packages_info :: instance();
    $p->load_packages();
  }
  
  protected function _register_filters($filter_chain)
  {
    $f = array();
    
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/core/filters/output_buffering_filter');    
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/core/filters/session_startup_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/core/filters/locale_definition_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/core/filters/authentication_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/core/filters/full_page_cache_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/core/filters/jip_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/core/filters/image_cache_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/core/filters/site_object_controller_filter');
  }
  
  protected function _register_file_resolvers()
  {
    $r = array();
    
    register_file_resolver('intercepting_filter', $r[] = LIMB_DIR . '/class/core/file_resolvers/intercepting_filter_file_resolver');
    register_file_resolver('ini',                 $r[] = LIMB_DIR . '/class/core/file_resolvers/ini_file_resolver');
    register_file_resolver('action',              $r[] = LIMB_DIR . '/class/core/file_resolvers/action_file_resolver');
    register_file_resolver('strings',             $r[] = LIMB_DIR . '/class/core/file_resolvers/strings_file_resolver');
    register_file_resolver('template',            $r[] = LIMB_DIR . '/class/core/file_resolvers/template_file_resolver');
    register_file_resolver('controller',          $r[] = LIMB_DIR . '/class/core/file_resolvers/controller_file_resolver');
    register_file_resolver('db_table',            $r[] = LIMB_DIR . '/class/core/file_resolvers/db_table_file_resolver');
    register_file_resolver('datasource',          $r[] = LIMB_DIR . '/class/core/file_resolvers/datasource_file_resolver');
    register_file_resolver('site_object',         $r[] = LIMB_DIR . '/class/core/file_resolvers/site_object_file_resolver');
  }
    
  public function run()
  {
    $this->_load_packages();
    
    $this->_register_file_resolvers();
    
    $filter_chain =& new filter_chain($this->request, $this->response);
    
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