<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: controller_group_access_template_datasource.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/datasource/datasource.class.php');

class object_controller_datasource extends datasource
{
  function _get_controller_id()
  {
    $request = request :: instance();
    if($controller_id = $request->get_attribute('controller_id'))
      return $controller_id;

    if($object_id = $request->get_attribute('object_id'))
      $object_data =& fetch_one_by_id($object_id);
    else
      $object_data =& fetch_requested_object();

    return $object_data['controller_id'];
  }  
}
?>