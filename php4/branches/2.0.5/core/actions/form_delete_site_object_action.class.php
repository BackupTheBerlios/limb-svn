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
require_once(LIMB_DIR . 'core/actions/form_site_object_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/tree_identifier_rule.class.php');

class form_delete_site_object_action extends form_site_object_action
{
	var $definition = array(
		'site_object' => 'site_object',
	);
	
	function form_delete_site_object_action($name='', $merge_definition=array())
	{
		parent :: form_site_object_action($name, $merge_definition);
	}
	
	function _init_dataspace()
	{
		$object_data =& fetch_mapped_by_url();
	
		$object =& site_object_factory :: create($this->definition['site_object']);
		$object->import_attributes($object_data);

		if($object->can_delete())
			return true;
		else
		{
			message_box :: write_notice('Can not be deleted!');
			close_popup();
		}	
	}
	
	function _valid_perform()
	{
		$object_data =& fetch_mapped_by_url();
	
		$object =& site_object_factory :: create($this->definition['site_object']);
		
		$object->import_attributes($object_data);
		
		if(!$object->delete())
			return false;

		$parent_object_data = fetch_one_by_node_id($object_data['parent_id']);

		close_popup(null, true);
	}

}

?>
