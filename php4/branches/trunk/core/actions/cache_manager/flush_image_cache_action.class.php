<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/actions/action.class.php');
require_once(LIMB_DIR . '/core/cache/full_page_cache_manager.class.php');
require_once(LIMB_DIR . '/core/cache/partial_page_cache_manager.class.php');
require_once(LIMB_DIR . '/core/cache/image_cache_manager.class.php');

class flush_image_cache_action extends action
{
  function perform(&$request, &$response)
  {
    $manager = new full_page_cache_manager();
    $manager->flush();

    $manager = new partial_page_cache_manager();
    $manager->flush();

    $manager = new image_cache_manager();
    $manager->flush();

    $request->set_status(REQUEST_STATUS_SUCCESS);

    if($request->has_attribute('popup'))
      $response->write(close_popup_response($request));
  }
}

?>