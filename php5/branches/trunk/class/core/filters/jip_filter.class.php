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
require_once(LIMB_DIR . 'class/core/fetcher.class.php');
require_once(LIMB_DIR . 'class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . 'class/core/permissions/user.class.php');
require_once(LIMB_SIMPLE_PERMISSIONS_DIR . '/simple_authenticator.class.php');

class jip_filter implements intercepting_filter 
{ 
  public function run($filter_chain, $request, $response) 
  {
    debug :: add_timing_point('jip filter started');

    $fetcher = LimbToolsBox :: getToolkit()->getFetcher();
        
    $fetcher->set_jip_status(false);
    
    if (LimbToolsBox :: getToolkit()->getUser()->is_logged_in())
    {
      $ini = get_ini('jip_groups.ini');
      
      if(simple_authenticator :: is_user_in_groups(array_keys($ini->get_group('groups'))))
        $fetcher->set_jip_status(true);
    }

    debug :: add_timing_point('jip filter done');
    
    $filter_chain->next();
  }   
} 
?>