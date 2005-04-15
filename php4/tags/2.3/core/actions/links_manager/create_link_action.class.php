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
require_once(LIMB_DIR . '/core/model/links_manager.class.php');

class create_link_action extends form_action
{
  function _define_dataspace_name()
  {
    return 'link';
  }

  function _init_validator()
  {
    parent :: _init_validator();

    $this->validator->add_rule($v1 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'target_node_id'));
    $this->validator->add_rule($v2 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'group_id'));
    $this->validator->add_rule($v3 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'linker_node_id'));
  }

  function _valid_perform(&$request, &$response)
  {
    $links_manager = new links_manager();

    $result = $links_manager->create_link(
        $this->dataspace->get('group_id'),
        $this->dataspace->get('linker_node_id'),
        $this->dataspace->get('target_node_id')
    );

    if ($result !== false)
    {
      $request->set_status(REQUEST_STATUS_FORM_SUBMITTED);

      if($request->has_attribute('popup'))
        $response->write(close_popup_response($request));
    }
    else
      $request->set_status(REQUEST_STATUS_FAILURE);
  }
}

?>