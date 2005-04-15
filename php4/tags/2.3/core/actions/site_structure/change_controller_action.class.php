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
require_once(LIMB_DIR . '/core/actions/form_action.class.php');

class change_controller_action extends form_action
{
  function _define_dataspace_name()
  {
    return 'change_controller_form';
  }

  function _init_validator()
  {
    $v = array();

    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/invalid_value_rule', 'controller_name', 0));
    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/invalid_value_rule', 'controller_name', -1));
  }

  function _init_dataspace(&$request)
  {
    parent :: _init_dataspace($request);

    $this->_transfer_dataspace($request);

    $node_id = $this->dataspace->get('id');

    if(!$object = wrap_with_site_object(fetch_one_by_node_id($node_id)))
      return;

    $controller = $object->get_controller();
    $this->dataspace->set('controller_name', get_class($controller));
  }

  function _valid_perform(&$request, &$response)
  {
    $node_id = $this->dataspace->get('id');

    if(!$object = wrap_with_site_object(fetch_one_by_node_id($node_id)))
    {
      $request->set_status(REQUEST_STATUS_FAILURE);
      return;
    }

    $object->set_attribute('controller_id', site_object_controller :: get_id($this->dataspace->get('controller_name')));

    $object->update(false);

    $request->set_status(REQUEST_STATUS_SUCCESS);

    if($request->has_attribute('popup'))
      $response->write(close_popup_response($request));
  }
}

?>