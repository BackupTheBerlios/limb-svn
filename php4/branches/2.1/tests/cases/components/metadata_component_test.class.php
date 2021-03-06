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
require_once(LIMB_DIR . '/tests/cases/limb_test.class.php');
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . 'core/template/components/metadata_component.class.php');

Mock :: generatePartial(
  'metadata_component',
  'metadata_component_test_version',
  array('_get_mapped_controller')
);

Mock::generate('site_object_controller');

class metadata_component_test extends limb_test 
{
	var $dump_file = 'metadata.sql';
	
	var $object = null;
	var $controller = null;

	var $parent_node_id = '';
	var $sub_node_id = '';
	var $sub_node_id2 = '';
	
  function setUp()
  {
  	parent :: setUp();
  	
  	$this->object =& new metadata_component_test_version($this);
  	$this->object->metadata_component();
  	
  	$this->controller =& new Mocksite_object_controller($this);
  	
  	$this->object->setReturnReference('_get_mapped_controller', $this->controller);
  	
  	$tree =& tree :: instance();
  	
		$values['identifier'] = 'object_300';
		$values['object_id'] = 300;
		$root_node_id = $tree->create_root_node($values, false, true);

		$values['identifier'] = 'object_301';
		$values['object_id'] = 301;
		$this->parent_node_id = $tree->create_sub_node($root_node_id, $values);

		$values['identifier'] = 'object_302';
		$values['object_id'] = 302;
		$this->sub_node_id = $tree->create_sub_node($this->parent_node_id, $values);

		$values['identifier'] = 'object_303';
		$values['object_id'] = 303;
		$this->sub_node_id2 = $tree->create_sub_node($root_node_id, $values);
  }
  
  function tearDown()
  {
  	parent :: tearDown();
  	
  	$this->object->tally();
  	$this->controller->tally();
  }
  
  function test_get_complete_object_metadata()
  {
		$this->object->set_node_id($this->sub_node_id);
		$this->object->load_metadata();
		$this->assertEqual($this->object->get_keywords(), 'object_302_keywords');
		$this->assertEqual($this->object->get_description(), 'object_302_description');
	}
		
  function test_get_partial_object_metadata()
  {
		$this->object->set_node_id($this->parent_node_id);
		$this->object->load_metadata();
		$this->assertEqual($this->object->get_keywords(), 'object_301_keywords');
		$this->assertEqual($this->object->get_description(), 'object_300_description');
	}	

  function test_get_parent_object_metadata()
  {
		$this->object->set_node_id($this->sub_node_id2);
		$this->object->load_metadata();
		$this->assertEqual($this->object->get_keywords(), 'object_300_keywords');
		$this->assertEqual($this->object->get_description(), 'object_300_description');
  }    

  function test_get_title()
  {
		$this->object->set_node_id($this->sub_node_id);
		$this->object->set_title_separator(' - ');

		$this->assertEqual($this->object->get_title(), 'object_302_title - object_301_title - object_300_title');
  }

  function test_get_breadcrums()
  {
  	$this->controller->expectOnce('determine_action');
  	$this->controller->setReturnValue('determine_action', false);
  	$this->controller->expectNever('get_action_property');
  	
		$this->object->set_node_id($this->sub_node_id);
		$breadcrumbs = $this->object->get_breadcrumbs_dataset();

		$this->assertNull($breadcrumbs->get('is_last'));

		$paths = array('object_300', 'object_301', 'object_302');
		$path = '/';
		$breadcrumbs->reset();

		for($i=1; $i <= $breadcrumbs->get_total_row_count(); $i++)
		{
			$breadcrumbs->next();
			$path .= current($paths) . '/';
			next($paths);
			$this->assertEqual($breadcrumbs->get('path'), $path);
			
			if ($i == $breadcrumbs->get_total_row_count())
				$this->assertTrue($breadcrumbs->get('is_last'));
		}
  }
  
  function test_get_breadcrums_offset_path()
  {
  	$this->controller->expectOnce('determine_action');
  	$this->controller->setReturnValue('determine_action', false);
  	$this->controller->expectNever('get_action_property');
  	
		$this->object->set_node_id($this->sub_node_id);
		$this->object->set_offset_path('/object_300/object_301/');
		
		$breadcrumbs = $this->object->get_breadcrumbs_dataset();

		$paths = array('object_302');
		$path = '/object_300/object_301/';
		$breadcrumbs->reset();

		for($i=1; $i <= $breadcrumbs->get_total_row_count(); $i++)
		{
			$breadcrumbs->next();
			$path .= current($paths) . '/';
			next($paths);
			$this->assertEqual($breadcrumbs->get('path'), $path);
			
			if ($i == $breadcrumbs->get_total_row_count())
				$this->assertTrue($breadcrumbs->get('is_last'));
		}
  }
  
  function test_get_breadcrums_with_action()
  {
  	$this->controller->expectOnce('determine_action');
  	$this->controller->setReturnValue('determine_action', 'action_test');
  	$this->controller->setReturnValue('get_action_property', true, array('action_test', 'display_in_breadcrumbs'));
  	$this->controller->expectOnce('get_default_action');
  	$this->controller->setReturnValue('get_default_action', 'default_action_test');
  	$this->controller->expectOnce('get_action_name');
  	$this->controller->setReturnValue('get_action_name', 'Action Name', array('action_test'));
  	
		$this->object->set_node_id($this->sub_node_id);
		$breadcrumbs = $this->object->get_breadcrumbs_dataset();
		
		$breadcrumbs->reset();
		while($breadcrumbs->next())
		{
			$path = $breadcrumbs->get('path');
			$title = $breadcrumbs->get('title');
		}
		
		$this->assertEqual('/object_300/object_301/object_302/?action=action_test', $path);
		$this->assertEqual('Action Name', $title);
  }
  
  function test_get_breadcrums_with_no_default_action()
  {
  	$this->controller->expectOnce('determine_action');
  	$this->controller->setReturnValue('determine_action', 'action_test');
  	$this->controller->setReturnValue('get_action_property', true, array('action_test', 'display_in_breadcrumbs'));
  	$this->controller->expectOnce('get_default_action');
  	$this->controller->setReturnValue('get_default_action', 'action_test');
  	$this->controller->expectNever('get_action_name');
  	
		$this->object->set_node_id($this->sub_node_id);
		$breadcrumbs = $this->object->get_breadcrumbs_dataset();		
  }

  function test_get_breadcrums_with_no_action()
  {
  	$this->controller->expectOnce('determine_action');
  	$this->controller->setReturnValue('determine_action', 'action_test');
  	$this->controller->setReturnValue('get_action_property', false, array('action_test', 'display_in_breadcrumbs'));
  	$this->controller->expectNever('get_default_action');
  	$this->controller->expectNever('get_action_name');
  	
		$this->object->set_node_id($this->sub_node_id);
		$breadcrumbs = $this->object->get_breadcrumbs_dataset();		
  }
  
  function test_get_node_id()
  {
  	$php_self = $_SERVER['PHP_SELF'];
  	$_SERVER['PHP_SELF'] = '/object_300/object_301';
  	
  	$node_id = $this->object->get_node_id();
  	
  	$tree =& tree :: instance();
  	$node =& $tree->get_node_by_path($_SERVER['PHP_SELF']);
  	$this->assertEqual($node_id, $node['id']);

  	$_SERVER['PHP_SELF'] = $php_self;
  }
}

?>