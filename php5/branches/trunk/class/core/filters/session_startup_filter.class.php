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
require_once(LIMB_DIR . '/class/core/filters/intercepting_filter.interface.php');

class session_startup_filter implements intercepting_filter 
{ 
  public function run($filter_chain, $request, $response) 
  {
    debug :: add_timing_point('session startup filter started');
    
    Limb :: toolkit()->getSession()->start();    

    debug :: add_timing_point('session startup filter finished');
    
    $filter_chain->next();
  }   
} 
?>
