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
require_once(LIMB_DIR . 'core/model/session_history_manager.class.php');

class session_history_filter extends intercepting_filter 
{ 
  function run(&$filter_chain, &$request, &$response) 
  {
    session_history_manager :: save();
    $filter_chain->next();
  }   
} 
?>