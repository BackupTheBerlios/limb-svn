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
require_once(LIMB_DIR . '/class/filters/intercepting_filter.class.php');

class session_startup_filter extends intercepting_filter 
{ 
  function run(&$filter_chain, &$request, &$response) 
  {
    debug :: add_timing_point('session startup filter started');
    
    require_once(LIMB_DIR . 'core/lib/session/session.class.php');    
    start_user_session();
              
    debug :: add_timing_point('session startup filter finished');
    
    $filter_chain->next();
  }   
} 
?>