<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/model/response/close_popup_response.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php');
require_once(LIMB_DIR . 'core/model/links_manager.class.php');

class create_link_action extends form_action
{
	function _define_dataspace_name()
	{
	  return 'link';
	}

	function _init_dataspace()
	{		  
	  $this->dataspace->set('target_node_id', -1000);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('target_node_id'));
		$this->validator->add_rule(new required_rule('group_id'));
		$this->validator->add_rule(new required_rule('linker_node_id'));
	}
	
	function _valid_perform()
	{
	  $links_manager = new links_manager();
	  
	  $result = $links_manager->create_link(
	      $this->dataspace->get('group_id'),
	      $this->dataspace->get('linker_node_id'),
	      $this->dataspace->get('target_node_id')
    );

    if ($result !== false)
		  return new close_popup_response(RESPONSE_STATUS_FORM_SUBMITTED);
    else
		  return new failed_response();
	}
}

?>