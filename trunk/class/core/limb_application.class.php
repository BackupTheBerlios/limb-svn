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
require_once(LIMB_DIR . 'class/filters/filter_chain.class.php');
require_once(LIMB_DIR . 'class/request/http_response.class.php');
require_once(LIMB_DIR . 'class/request/request.class.php');
require_once(LIMB_DIR . 'class/limb_util.inc.php');
require_once(LIMB_DIR . 'class/lib/system/message_box.class.php');

class limb_application
{  
  function _register_filters(&$filter_chain)
  {
    $f = array();
    
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/filters/output_buffering_filter');    
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/filters/session_startup_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/filters/locale_definition_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/filters/authentication_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/filters/logging_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/filters/full_page_cache_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/filters/jip_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/filters/image_cache_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'class/filters/site_object_controller_filter');
  }
    
  function run()
  {
    $request =& request :: instance();
    $response =& new http_response();
    
    $filter_chain =& new filter_chain($request, $response);
    
    $this->_register_filters($filter_chain);
    
    $filter_chain->process();
    
    if( $response->get_content_type() == 'text/html' && 
        $response->get_status() == 200)//only 200?
    {
      if (debug :: is_console_enabled())
      	$response->write(debug :: parse_html_console());
      	
      $response->write(message_box :: parse());//It definetly should be somewhere else!
    }
        
    $response->commit();      
  }
}

?>