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
require_once(LIMB_DIR . '/class/core/filters/intercepting_filter.class.php');

class output_buffering_filter extends intercepting_filter 
{      
  function run(&$filter_chain, &$request, &$response)
  { 
    ob_start();
    
    $filter_chain->next();
    
    if($response->is_empty() && ($content = ob_get_contents()))
      $response->write($content);
  
    if(ob_get_level())
      ob_end_clean();
  }      
}
?>