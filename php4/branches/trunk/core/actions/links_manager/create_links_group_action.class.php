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
require_once(LIMB_DIR . '/core/actions/form_action.class.php');
require_once(LIMB_DIR . '/core/model/links_manager.class.php');

class create_links_group_action extends form_action
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
	}
	
	function _valid_perform(&$request, &$response)
	{
	  $links_manager = new links_manager();
	  
	  $result = $links_manager->create_links_group(
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