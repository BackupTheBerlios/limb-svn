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
require_once(LIMB_DIR . '/core/lib/validators/rules/required_rule.class.php');
require_once(LIMB_DIR . '/core/model/links_manager.class.php');

class edit_links_group_action extends form_action
{
  function _define_dataspace_name()
  {
    return 'links_group';
  }

  function _init_validator()
  {
    parent :: _init_validator();

    $this->validator->add_rule($v1 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'identifier'));
    $this->validator->add_rule($v2 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'title'));
    $this->validator->add_rule($v3 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'group_id'));
  }

  function _init_dataspace(&$request)
  {
    if (!$group_id = $request->get_attribute('group_id'))
      return false;

    $links_manager = new links_manager();

    if($group_data = $links_manager->fetch_group($group_id))
      $this->dataspace->import($group_data);
  }

  function _valid_perform(&$request, &$response)
  {
    if (!$this->dataspace->get('group_id'))
    {
      $request->set_status(REQUEST_STATUS_FAILURE);
      return;
    }

    $links_manager = new links_manager();

    $result = $links_manager->update_links_group(
        $this->dataspace->get('group_id'),
        $this->dataspace->get('identifier'),
        $this->dataspace->get('title')
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