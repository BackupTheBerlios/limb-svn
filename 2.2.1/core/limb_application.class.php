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
require_once(LIMB_DIR . 'core/lib/debug/debug.class.php');

debug :: add_timing_point('start');

require_once(LIMB_DIR . 'core/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . 'core/filters/filter_chain.class.php');
require_once(LIMB_DIR . 'core/request/http_response.class.php');
require_once(LIMB_DIR . 'core/request/request.class.php');
require_once(LIMB_DIR . 'core/lib/http/control_flow.inc.php');
require_once(LIMB_DIR . 'core/lib/system/message_box.class.php');

class limb_application
{
  function _inititiliaze_user_session()
  {
    require_once(LIMB_DIR . 'core/lib/session/session.class.php');
    start_user_session();
  }
  
  function _register_filters(&$filter_chain)
  {
    $f = array();
    $filter_chain->register_filter($f[] = LIMB_DIR . 'core/filters/locale_definition_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'core/filters/authentication_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'core/filters/logging_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'core/filters/full_page_cache_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'core/filters/jip_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'core/filters/output_buffering_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'core/filters/image_cache_filter');
    $filter_chain->register_filter($f[] = LIMB_DIR . 'core/filters/site_object_controller_filter');
  }
    
  function run()
  {
    $this->_inititiliaze_user_session();
    
    $request =& request :: instance();
    $response =& new http_response();
    
    $filter_chain =& new filter_chain($request, $response);
    
    $this->_register_filters($filter_chain);
    
    $filter_chain->process();
    
    if(!$response->file_sent())//FIXXX???
    {
      if (debug :: is_console_enabled())
      	echo debug :: parse_html_console();
      	
      echo message_box :: parse();//It definetly should be somewhere else!
    }
    
    $response->commit();      
  }
}

?>