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
require_once(LIMB_DIR . '/tests/cases/db_test.class.php');
require_once(LIMB_DIR . '/core/model/access_policy.class.php');
require_once(LIMB_DIR . '/core/model/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/core/controllers/site_object_controller.class.php');

Mock :: generate('site_object');
Mock :: generate('site_object_controller');

class save_object_access_policy_test extends db_test
{
  var $dump_file = 'access_policy_load.sql';

  var $ac = null;
  var $object = null;
  var $parent_object = null;
  var $parent_object_controller = null;

  function setUp()
  {
    parent :: setUp();

    $this->ac =& access_policy :: instance();
    $this->object =& new Mocksite_object($this);
    $this->parent_object =& new Mocksite_object($this);
    $this->parent_object_controller =& new Mocksite_object_controller($this);

    $this->object->expectOnce('get_id');
    $this->parent_object->expectOnce('get_id');
    $this->parent_object->expectOnce('get_controller_id');
    $this->parent_object->expectOnce('get_controller');
    $this->parent_object->setReturnReference('get_controller', $this->parent_object_controller);
  }

  function tearDown()
  {
    parent :: tearDown();

    $this->object->tally();
    $this->parent_object->tally();
    $this->parent_object_controller->tally();
  }

  function test_save_object_access_wrong_class_no_parent_records()
  {
    $this->object->setReturnValue('get_id', -1);
    $this->parent_object->setReturnValue('get_id', -2);
    $this->parent_object->setReturnValue('get_controller_id', -2);
    $this->parent_object_controller->setReturnValue('determine_action', 'display');

    debug_mock :: expect_write_error('parent object has no acccess records at all',
      array(
        'parent_id' => -2
      )
    );

    $this->assertFalse($this->ac->save_initial_object_access($this->object, $this->parent_object));
  }

  function test_save_object_access_wrong_action_no_parent_records()
  {
    $this->object->setReturnValue('get_id', -1);
    $this->parent_object->setReturnValue('get_id', -2);
    $this->parent_object->setReturnValue('get_controller_id', 10);
    $this->parent_object_controller->setReturnValue('determine_action', 'display');

    debug_mock :: expect_write_error('parent object has no acccess records at all',
      array(
        'parent_id' => -2
      )
    );

    $this->assertFalse($this->ac->save_initial_object_access($this->object, $this->parent_object));
  }

  function test_save_object_access_save_group_template()
  {
    $this->object->setReturnValue('get_id', 305);
    $this->parent_object->setReturnValue('get_id', -2);
    $this->parent_object->setReturnValue('get_controller_id', 10);
    $this->parent_object_controller->setReturnValue('determine_action', 'create');

    $this->assertTrue($this->ac->save_initial_object_access($this->object, $this->parent_object));

    $group_objects_access = $this->ac->get_object_access_by_ids(array(305), ACCESSOR_TYPE_GROUP);

    $this->assertEqual($group_objects_access[305],
      array(
        100 => 1,
        110 => 1,
      )
    );

    $user_objects_access = $this->ac->get_object_access_by_ids(array(305), ACCESSOR_TYPE_USER);
    $this->assertEqual(sizeof($user_objects_access), 0);
  }

  function test_save_object_access_save_user_template()
  {
    $this->object->setReturnValue('get_id', 305);
    $this->parent_object->setReturnValue('get_id', -2);
    $this->parent_object->setReturnValue('get_controller_id', 11);
    $this->parent_object_controller->setReturnValue('determine_action', 'publish');
    $this->assertTrue($this->ac->save_initial_object_access($this->object, $this->parent_object));

    $user_objects_access = $this->ac->get_object_access_by_ids(array(305), ACCESSOR_TYPE_USER);

    $this->assertEqual($user_objects_access[305], array(200 => 1));

    $group_objects_access = $this->ac->get_object_access_by_ids(array(305), ACCESSOR_TYPE_GROUP);
    $this->assertEqual(sizeof($group_objects_access), 0);
  }

  function test_save_object_access_save_template()
  {
    $this->object->setReturnValue('get_id', 305);
    $this->parent_object->setReturnValue('get_id', -2);
    $this->parent_object->setReturnValue('get_controller_id', 11);
    $this->parent_object_controller->setReturnValue('determine_action', 'create');

    $this->assertTrue($this->ac->save_initial_object_access($this->object, $this->parent_object));

    $user_objects_access = $this->ac->get_object_access_by_ids(array(305), ACCESSOR_TYPE_USER);

    $this->assertEqual($user_objects_access[305],
      array(
        200 => 1,
        210 => 1,
      )
    );

    $group_objects_access = $this->ac->get_object_access_by_ids(array(305), ACCESSOR_TYPE_GROUP);

    $this->assertEqual($group_objects_access[305],
      array(
        100 => 1,
        110 => 1,
      )
    );
  }

  function test_save_object_access_save_template_for_defined_action()
  {
    $this->object->setReturnValue('get_id', 305);
    $this->parent_object->setReturnValue('get_id', -2);
    $this->parent_object->setReturnValue('get_controller_id', 11);

    $this->parent_object->expectCallCount('get_controller', 0);

    $this->assertTrue($this->ac->save_initial_object_access($this->object, $this->parent_object, 'create'));

    $user_objects_access = $this->ac->get_object_access_by_ids(array(305), ACCESSOR_TYPE_USER);

    $this->assertEqual($user_objects_access[305],
      array(
        200 => 1,
        210 => 1,
      )
    );

    $group_objects_access = $this->ac->get_object_access_by_ids(array(305), ACCESSOR_TYPE_GROUP);

    $this->assertEqual($group_objects_access[305],
      array(
        100 => 1,
        110 => 1,
      )
    );
  }

  function test_save_object_access_copy_from_parent()
  {
    $this->object->setReturnValue('get_id', 305);
    $this->parent_object->setReturnValue('get_id', 300);
    $this->parent_object->setReturnValue('get_controller_id', -1);
    $this->parent_object_controller->setReturnValue('determine_action', 'no_such_action');

    $this->assertTrue($this->ac->save_initial_object_access($this->object, $this->parent_object));

    $user_objects_access = $this->ac->get_object_access_by_ids(array(305), ACCESSOR_TYPE_USER);

    $this->assertEqual($user_objects_access[305],
      array(
        200 => 1,
        210 => 1,
      )
    );

    $group_objects_access = $this->ac->get_object_access_by_ids(array(305), ACCESSOR_TYPE_GROUP);

    $this->assertEqual($group_objects_access[305],
      array(
        100 => 1,
        110 => 1,
      )
    );
  }

  function test_save_object_access_for_action()
  {
    $this->object->setReturnValue('get_id', 305);
    $this->object->setReturnValue('get_controller_id', 11);

    $this->parent_object->expectCallCount('get_id', 0);
    $this->parent_object->expectCallCount('get_controller_id', 0);
    $this->parent_object->expectCallCount('get_controller', 0);

    $this->assertTrue($this->ac->save_object_access_for_action($this->object, 'create'));

    $user_objects_access = $this->ac->get_object_access_by_ids(array(305), ACCESSOR_TYPE_USER);

    $this->assertEqual($user_objects_access[305],
      array(
        200 => 1,
        210 => 1,
      )
    );

    $group_objects_access = $this->ac->get_object_access_by_ids(array(305), ACCESSOR_TYPE_GROUP);

    $this->assertEqual($group_objects_access[305],
      array(
        100 => 1,
        110 => 1,
      )
    );

  }
}

?>