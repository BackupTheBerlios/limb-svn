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
require_once(LIMB_DIR . '/class/core/filters/intercepting_filter.interface.php');

class logging_filter implements intercepting_filter 
{
  public function run($filter_chain, $request, $response)
  { 
    $filter_chain->next();
    
    debug :: add_timing_point('logging filter started');
    
    $object = wrap_with_site_object(fetch_requested_object($request));
    
    $controller = $object->get_controller();
    
    include_once(dirname(__FIlE__) . '/../stats_register.class.php');
    
    $stats_register = new stats_register(); 
    
    $stats_register->register(
      $object->get_node_id(), 
      $controller->determine_action(), 
      $request->get_status()
    );
      
    debug :: add_timing_point('logging filter finished');        
  }
}
?>