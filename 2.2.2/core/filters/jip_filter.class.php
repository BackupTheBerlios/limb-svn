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
require_once(LIMB_DIR . 'core/fetcher.class.php');
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');

class jip_filter extends intercepting_filter 
{ 
  function run(&$filter_chain, &$request, &$response) 
  {
    debug :: add_timing_point('jip filter started');
  
    $fetcher =& fetcher :: instance();
    $fetcher->set_jip_status(false);
    
    $user =& user :: instance();
    
    if ($user->is_logged_in())
    {
      $ini =& get_ini('jip_groups.ini');
      
      if($user->is_in_groups(array_keys($ini->get_group('groups'))))
        $fetcher->set_jip_status(true);
    }

    debug :: add_timing_point('jip filter done');

    $filter_chain->next();
  }   
} 
?>