<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_article_action.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/actions/form_action.class.php');

class display_detail_info_action extends form_action
{
  function _define_dataspace_name()
  {
    return 'detail_info_form';
  }
  
  function _init_dataspace(&$request)
  {
    $object_data =& $this->_load_object_data();
    $this->dataspace->set('object_id', $object_data['object_id']);
  }
  
  function _transfer_dataspace(&$request)
  {
    parent :: _transfer_dataspace($request);
    if($object_id = $this->dataspace->get('object_id'))
      $request->set_attribute('object_id', $object_id);

  }
  
  function & _load_object_data()
  {
    $request =& request :: instance();

    if($object_id = $request->get_attribute('object_id'))
      return fetch_one_by_id($object_id);
    else
      return fetch_requested_object();
  }
}
?>