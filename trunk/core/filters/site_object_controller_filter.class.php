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
require_once(LIMB_DIR . '/core/filters/intercepting_filter.class.php');

class site_object_controller_filter extends intercepting_filter 
{ 
  function run(&$filter_chain, &$request, &$response) 
  {  
    $site_object =& wrap_with_site_object(fetch_requested_object($request));
        
    debug :: add_timing_point('object fetched');
    
    $site_object_controller =& $site_object->get_controller();
            
    $site_object_controller->process($request, $response);
    
    if($response->is_empty())
      $site_object_controller->display_view();

    $filter_chain->next(); 
  } 
} 
?>