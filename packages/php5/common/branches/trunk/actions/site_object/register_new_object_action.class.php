<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'class/core/actions/form_action.class.php');

class register_new_object_action extends form_action
{
	protected function _define_dataspace_name()
	{
	  return 'register_new_object';
	}
	
	protected function _init_validator()
	{
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'class_name')); 
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'identifier')); 
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'parent_path')); 
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/tree_path_rule', 'parent_path')); 
	
		if($path = $this->dataspace->get('parent_path'))
		{
			if($node = LimbToolsBox :: getToolkit()->getTree()->get_node_by_path($path))
        $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/tree_identifier_rule', 'identifier', $node['id'])); 
		}
		
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'title')); 
	}
	
	protected function _valid_perform($request, $response)
	{
		$params = array();
		
		$params['identifier'] = $this->dataspace->get('identifier');
		$params['parent_path'] = $this->dataspace->get('parent_path');
		$params['class'] = $this->dataspace->get('class_name');
		$params['title'] = $this->dataspace->get('title');
		
		$object = LimbToolsBox :: getToolkit()->createSiteObject($params['class']);
		
		$is_root = false;
		if(!$parent_data = LimbToolsBox :: getToolkit()->getFetcher()->fetch_one_by_path($params['parent_path']))
		{
			if ($params['parent_path'] == '/')
				$is_root = true;
			else
			{
  	    message_box :: write_notice('parent wasn\'t retrieved by path ' . $params['parent_path']);
  	    $request->set_status(request :: STATUS_FAILURE);
  	    return;
  	  }
		}
		
		if (!$is_root)
			$params['parent_node_id'] = $parent_data['node_id'];
		else	
			$params['parent_node_id'] = 0;		
			
		$object->merge($params);
	
	  try
	  {
		   $object->create($is_root);
		}
		catch(LimbException $e)
		{
 	    message_box :: write_notice('object wasn\'t registered!');
	    $request->set_status(request :: STATUS_FAILURE);
	    throw $e; 
		}
		
		if (!$is_root)
		{
			$parent_object = LimbToolsBox :: getToolkit()->createSiteObject($parent_data['class_name']);
			$parent_object->merge($parent_data);

	  	$action = $parent_object->get_controller()->determine_action();
  
      $access_policy = new access_policy(); 
      $access_policy->save_new_object_access($object, $parent_object, $action);
		}	

		$request->set_status(request :: STATUS_FORM_SUBMITTED);

		if($request->has_attribute('popup'))
			$response->write(close_popup_response($request));
	}
}

?>