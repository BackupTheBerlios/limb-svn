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

class locale_definition_filter extends intercepting_filter 
{ 
  function run(&$filter_chain, &$request, &$response) 
  {
    debug :: add_timing_point('locale filter started');
    
    if(!$node = map_request_to_node($request))
    {
    	if(!defined('CONTENT_LOCALE_ID'))
    	  define('CONTENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID);
    	if(!defined('MANAGEMENT_LOCALE_ID'))
    	  define('MANAGEMENT_LOCALE_ID', CONTENT_LOCALE_ID);
    	
    	$locale =& locale :: instance();    	
    	$locale->setlocale();
    	
      $filter_chain->next();
      return;
    }
          
    if(!defined('CONTENT_LOCALE_ID'))
    {
      if($object_locale_id = site_object :: get_locale_by_id($node['object_id']))
      	define('CONTENT_LOCALE_ID', $object_locale_id);
      else
        define('CONTENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID);
    }
    
    if(!defined('MANAGEMENT_LOCALE_ID'))
    {
      $user = user :: instance();
      if($user_locale_id = $user->get_locale_id())
      	define('MANAGEMENT_LOCALE_ID', $user_locale_id);
      else
        define('MANAGEMENT_LOCALE_ID', CONTENT_LOCALE_ID);
    }
              
    debug :: add_timing_point('locale filter finished');

  	$locale =& locale :: instance();    	
  	$locale->setlocale();
    
    $filter_chain->next();
  }   
} 
?>