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

class locale_definition_filter implements intercepting_filter
{
  public function run($filter_chain, $request, $response)
  {
    $toolkit = Limb :: toolkit();
        
    debug :: add_timing_point('locale filter started');
    
    $toolkit->getLocale()->setlocale();
    
    $datasource = $toolkit->getDatasource('requested_object_datasource');
    
    if(!$node = $datasource->map_request_to_node($request))
    {
      $toolkit->define('CONTENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID);
      $toolkit->define('MANAGEMENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID);

      $filter_chain->next();
      return;
    }
    
    if($object_locale_id = $this->_find_site_object_locale_id($node['object_id']))
      $toolkit->define('CONTENT_LOCALE_ID', $object_locale_id);
    else
      $toolkit->define('CONTENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID);

    if($user_locale_id = $toolkit->getUser()->get('locale_id'))
      $toolkit->define('MANAGEMENT_LOCALE_ID', $user_locale_id);
    else
      $toolkit->define('MANAGEMENT_LOCALE_ID', $toolkit->constant('CONTENT_LOCALE_ID'));

    debug :: add_timing_point('locale filter finished');

    $filter_chain->next();
  }
  
  //for mocking
  protected function _find_site_object_locale_id($object_id)
  {
    include_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
    return site_object :: find_object_locale_id($object_id);
  }  
}
?>