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

class locale_definition_filter implements intercepting_filter
{
  public function run($filter_chain, $request, $response)
  {
    debug :: add_timing_point('locale filter started');

    if(!$node = LimbToolsBox :: getToolkit()->getFetcher()->map_request_to_node($request))
    {
    	define('CONTENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID);
    	define('MANAGEMENT_LOCALE_ID', CONTENT_LOCALE_ID);

    	locale :: instance()->setlocale();

      $filter_chain->next();
      return;
    }

    if($object_locale_id = site_object :: get_locale_by_id($node['object_id']))
    	define('CONTENT_LOCALE_ID', $object_locale_id);
    else
      define('CONTENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID);

    if($user_locale_id = LimbToolsBox :: getToolkit()->getUser()->get('locale_id'))
    	define('MANAGEMENT_LOCALE_ID', $user_locale_id);
    else
      define('MANAGEMENT_LOCALE_ID', CONTENT_LOCALE_ID);

    debug :: add_timing_point('locale filter finished');

  	locale :: instance()->setlocale();

    $filter_chain->next();
  }
}
?>