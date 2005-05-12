<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: flush_ini_cache_action.class.php 1274 2005-05-03 10:11:45Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/actions/action.class.php');
require_once(LIMB_DIR . '/core/lib/system/fs.class.php');

class flush_general_cache_action extends action
{
  function perform(&$request, &$response)
  {
    $files = fs :: find(VAR_DIR . '/cache', 'f');
    foreach($files as $file)
      unlink($file);

    if($request->has_attribute('popup'))
      $response->write(close_popup_response($request));

    $request->set_status(REQUEST_STATUS_SUCCESS);
  }
}

?>