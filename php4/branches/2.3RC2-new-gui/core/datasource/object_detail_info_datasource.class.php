<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: cart_items_datasource.class.php 938 2004-12-04 09:23:34Z dbrain $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/datasource/datasource.class.php');

class object_detail_info_datasource extends datasource
{
  function & get_dataset(&$counter, $params=array())
  {
    return new array_dataset(array($this->_fetch_object_data()));
  }
  
  function & _fetch_object_data()
  {
    $request =& request :: instance();

    if($object_id = $request->get_attribute('object_id'))
      return fetch_one_by_id($object_id);
    else
      return fetch_requested_object();
  }
}
?>