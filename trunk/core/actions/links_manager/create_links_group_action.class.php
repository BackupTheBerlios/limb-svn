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

class create_links_group_action extends form_action
{
	function _define_dataspace_name()
	{
	  return 'links_group';
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('identifier'));
		$this->validator->add_rule(new required_rule('title'));
	}
	
	function _valid_perform()
	{
	  $links_manager = new links_manager();
	  
	  $result = $links_manager->create_links_group(
	      $this->dataspace->get('identifier'),
	      $this->dataspace->get('title')
    );

    if ($result !== false)
		  return new close_popup_response(RESPONSE_STATUS_FORM_SUBMITTED);
    else
		  return new failed_response();
	}
}

?>