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
require_once(LIMB_DIR . '/class/core/site_objects/site_object_controller.class.php');
require_once(LIMB_DIR . '/class/core/behaviours/site_object_behaviour.class.php');
require_once(dirname(__FILE__) . '/../../../simple_authorizer.class.php');
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/class/db_tables/db_table_factory.class.php');
require_once(dirname(__FILE__) . '/../../../access_policy.class.php');

class simple_authorizer_test_behaviour_test_version extends site_object_behaviour
{
  public function get_display_action_properties(){}
  public function get_create_action_properties(){}
  public function get_edit_action_properties(){}
  public function get_publish_action_properties(){}
  public function get_delete_action_properties(){}
}

Mock :: generate('simple_authorizer_test_behaviour_test_version');

Mock :: generatePartial('simple_authorizer',
                        'simple_authorizer_test_version',
                        array('get_user_accessor_ids'));

Mock :: generatePartial('simple_authorizer',
                        'simple_authorizer_test_version2',
                        array('get_user_accessor_ids',
                              'get_behaviour_accessible_actions',
                              '_get_behaviour'));

Mock :: generate('site_object_controller');
  
class simple_authorizer_test extends LimbTestCase 
{  	
  var $db;
  
  function setUp()
  {
    $this->clean_up();
  }
  
  function tearDown()
  {
  	user :: instance()->logout();
  	
    $this->clean_up();
  }
  
  function clean_up()
  {
    $db = db_factory :: instance();

    $db->sql_delete('sys_object_access');
    $db->sql_delete('sys_action_access');
  }
  
  function test_get_accessible_object_ids_no_user_accessor_ids()
  {
    $authorizer = new simple_authorizer_test_version($this);
    
    $this->assertEqual($authorizer->get_accessible_object_ids(array(300, 303)), array());
  }

  function test_get_accessible_object_ids_no_db_records()
  {
    $authorizer = new simple_authorizer_test_version($this);
    $authorizer->setReturnValue('get_user_accessor_ids', array(100, 200));
    $this->assertEqual($authorizer->get_accessible_object_ids(array(300, 303)), array());
  }

  function test_get_accessible_object_ids_ok()
  {
    $authorizer = new simple_authorizer_test_version($this);
    $authorizer->expectOnce('get_user_accessor_ids');
    $authorizer->setReturnValue('get_user_accessor_ids', array(100, 200));
    
    $db_table = db_table_factory :: create('sys_object_access');

    $db_table->insert(array('id' => 1, 
                           'object_id' => 300, 
                           'access' => 1,
                           'accessor_id' => 100, // this one is accessible
                           'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP));
    
    $db_table->insert(array('id' => 2, 
                           'object_id' => 302, 
                           'access' => 1,
                           'accessor_id' => 200, // this one is accessible
                           'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP));

    $db_table->insert(array('id' => 3, 
                           'object_id' => 301, 
                           'access' => 1,
                           'accessor_id' => 101, // this one is not accessible
                           'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP));

    $db_table->insert(array('id' => 4, 
                           'object_id' => 305, // this one is not accessible
                           'access' => 1,
                           'accessor_id' => 100, 
                           'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP));
    
    $result = array(300, 302);
    $this->assertEqual($authorizer->get_accessible_object_ids(array(300, 301, 302, 303)), $result);
    
    $authorizer->tally();
  }
  
  function test_get_behaviour_accesssible_actions_no_user_accessor_ids()
  {
    $authorizer = new simple_authorizer_test_version($this);
    $authorizer->setReturnValue('get_user_accessor_ids', array(100, 200));
    
    $behaviour_id = 10;
    $this->assertEqual($authorizer->get_behaviour_accessible_actions($behaviour_id), array());
  }

  function test_get_behaviour_accesssible_actions_no_db_records()
  {
    $authorizer = new simple_authorizer_test_version($this);
    $authorizer->setReturnValue('get_user_accessor_ids', array(100, 200));
    
    $behaviour_id = 10;
    $this->assertEqual($authorizer->get_behaviour_accessible_actions($behaviour_id), array());
  }
  
  function test_get_behaviour_accesssible_actions_ok()
  {
    $authorizer = new simple_authorizer_test_version($this);
    $authorizer->expectOnce('get_user_accessor_ids');
    $authorizer->setReturnValue('get_user_accessor_ids', array(100, 200));
                            
    $behaviour_id = 10;

    $db_table = db_table_factory :: create('sys_action_access');

    $db_table->insert(array('id' => 1, 
                           'behaviour_id' => $behaviour_id, 
                           'action_name' => 'create',
                           'accessor_id' => 100, // this one is accessible
                           'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP));

    $db_table->insert(array('id' => 2,  
                           'behaviour_id' => $behaviour_id, 
                           'action_name' => 'delete',
                           'accessor_id' => 200, // this one is accessible too
                           'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP));

    $db_table->insert(array('id' => 3,
                            'behaviour_id' => $behaviour_id,
                            'action_name' => 'edit',
                            'accessor_id' => 101, // this one is NOT accessible
                            'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP));

    $db_table->insert(array('id' => 4,
                            'behaviour_id' => 12, // this one is NOT accessible too
                            'action_name' => 'publich',
                            'accessor_id' => 101,
                            'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP));
    
    $result = array('create', 'delete');

    $this->assertEqual($authorizer->get_behaviour_accessible_actions($behaviour_id), $result);
    
    $authorizer->tally();
  }
  
 	function test_assign_actions_ok()
 	{
    $behaviour_id = 10;
    
    $behaviour = new Mocksimple_authorizer_test_behaviour_test_version($this);
    
    $behaviour->setReturnValue('get_display_action_properties', array('some display action data'));
    $behaviour->setReturnValue('get_create_action_properties', array('some create action data'));
    $behaviour->setReturnValue('get_edit_action_properties', array());
    $behaviour->setReturnValue('get_publish_action_properties', array());
    $behaviour->setReturnValue('get_delete_action_properties', array());
    
    $actions_list = array('display', 'create', 'edit', 'publish', 'delete');		
		$behaviour->setReturnValue('get_actions_list', $actions_list);
		
    $authorizer = new simple_authorizer_test_version2($this);
		$authorizer->expectOnce('get_behaviour_accessible_actions');
		$authorizer->setReturnValue('get_behaviour_accessible_actions',
                                array('create', 'display', 'edit', 'delete'),
                                array($behaviour_id));
    
		$authorizer->setReturnReference('_get_behaviour', $behaviour, array('test_behaviour'));

  	$objects_to_assign_actions = array(
  		1 => array(
  			'id' => 300,
  			'behaviour_id' => $behaviour_id,
  			'behaviour' => 'test_behaviour',
  		),
  	);
    
  	$authorizer->assign_actions_to_objects($objects_to_assign_actions);
  	
  	$obj = reset($objects_to_assign_actions);
  	$this->assertEqual(sizeof($obj['actions']), 4);
  	
  	$this->assertEqual($obj['actions'],
  		array(
  			'create' => array('some create action data'),
  			'display' => array('some display action data'),
  			'edit' => array(),
  			'delete' => array()
  		)
  	);	

  	$behaviour->tally();
    $authorizer->tally();
 	}
}

?>