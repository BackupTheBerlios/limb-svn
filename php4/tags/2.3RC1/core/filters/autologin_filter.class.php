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

class autologin_filter extends intercepting_filter
{
  function run(&$filter_chain, &$request, &$response)
  { 
		$user =& user :: instance();
		$user->try_autologin();     
    $filter_chain->next();        
  }
}
?>