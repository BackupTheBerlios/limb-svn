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
require_once(LIMB_DIR . 'class/template/components/actions_component.class.php');
require_once(LIMB_DIR . 'class/i18n/strings.class.php');

class actions_component_test extends LimbTestCase 
{    
  function test_get_actions()
  {
  	$j = new actions_component();
  	
  	$actions = array(
			'display' => array(),
			'edit' => array(
					'JIP' => false,
					'action_name' => strings :: get('edit'),
					'img_src' => '/shared/images/edit.gif',
			),
			'delete' => array(
					'JIP' => true,
					'action_name' => strings :: get('delete'),
					'img_src' => '/shared/images/rem.gif',
			),
  	);
  	
  	$j->set_actions($actions);
  	$j->set_node_id(100);
  	
  	$actions = $j->get_actions();
  	
  	$this->assertTrue(is_array($actions));
  	$this->assertEqual(count($actions), 1);
  	
  	$action = reset($actions);
  	$this->assertEqual($action['action'], 'delete');
  	
  	$this->assertWantedPattern('/^\/root\?.+$/', $action['action_href']);
  	$this->assertWantedPattern('/&*action=delete/', $action['action_href']);
  	$this->assertWantedPattern('/&*node_id=100/', $action['action_href']);
  }    
}

?>