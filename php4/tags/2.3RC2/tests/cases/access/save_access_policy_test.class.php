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

class save_access_policy_test extends db_test
{
  var $dump_file = 'access_policy_load.sql';

  var $ac = null;

  function setUp()
  {
    parent :: setUp();

    $this->ac =& access_policy :: instance();
  }

  function test_save_user_actions_access()
  {
    $policy = array(
      200 => array(
          'display' => 1,
          'create' => 1,
          'edit' => 1,
          'delete' => 1,
      ),
      210 => array(
          'display' => 1,
          'create' => 0,
          'edit' => 0,
          'delete' => 0,
      ),
    );

    $this->ac->save_action_access($controller_id = 10, $policy, ACCESSOR_TYPE_USER);

    $db_table	=  & db_table_factory :: instance('sys_action_access');

    $conditions['controller_id'] = $controller_id;
    $conditions['accessor_type'] = ACCESSOR_TYPE_USER;

    $rows = $db_table->get_list($conditions, 'id', null);

    $this->assertEqual(count($rows), 5);

    $this->assertEqual($rows,
      array(
        array('id' => $rows[0]['id'], 'controller_id' => 10, 'accessor_id' => 200, 'action_name' => 'display', 'accessor_type' => ACCESSOR_TYPE_USER),
        array('id' => $rows[1]['id'], 'controller_id' => 10, 'accessor_id' => 200, 'action_name' => 'create', 'accessor_type' => ACCESSOR_TYPE_USER),
        array('id' => $rows[2]['id'], 'controller_id' => 10, 'accessor_id' => 200, 'action_name' => 'edit', 'accessor_type' => ACCESSOR_TYPE_USER),
        array('id' => $rows[3]['id'], 'controller_id' => 10, 'accessor_id' => 200, 'action_name' => 'delete', 'accessor_type' => ACCESSOR_TYPE_USER),
        array('id' => $rows[4]['id'], 'controller_id' => 10, 'accessor_id' => 210, 'action_name' => 'display', 'accessor_type' => ACCESSOR_TYPE_USER),
      )
    );
  }

  function test_save_group_actions_access()
  {
    $policy = array(
      100 => array(
          'display' => 1,
          'create' => 1,
          'edit' => 1,
          'delete' => 1,
      ),
      110 => array(
          'display' => 1,
          'create' => 0,
          'edit' => 0,
          'delete' => 1,
      ),
    );

    $this->ac->save_action_access($controller_id = 10, $policy, ACCESSOR_TYPE_GROUP);

    $db_table	=  & db_table_factory :: instance('sys_action_access');

    $conditions['controller_id'] = $controller_id;
    $conditions['accessor_type'] = ACCESSOR_TYPE_GROUP;

    $rows = $db_table->get_list($conditions, 'id', null);

    $this->assertEqual(count($rows), 6);

    $this->assertEqual($rows,
      array(
        array('id' => $rows[0]['id'], 'controller_id' => 10, 'action_name' => 'display', 'accessor_id' => 100, 'accessor_type' => ACCESSOR_TYPE_GROUP),
        array('id' => $rows[1]['id'], 'controller_id' => 10, 'action_name' => 'create', 'accessor_id' => 100, 'accessor_type' => ACCESSOR_TYPE_GROUP),
        array('id' => $rows[2]['id'], 'controller_id' => 10, 'action_name' => 'edit', 'accessor_id' => 100, 'accessor_type' => ACCESSOR_TYPE_GROUP),
        array('id' => $rows[3]['id'], 'controller_id' => 10, 'action_name' => 'delete', 'accessor_id' => 100, 'accessor_type' => ACCESSOR_TYPE_GROUP),
        array('id' => $rows[4]['id'], 'controller_id' => 10, 'action_name' => 'display', 'accessor_id' => 110, 'accessor_type' => ACCESSOR_TYPE_GROUP),
        array('id' => $rows[5]['id'], 'controller_id' => 10, 'action_name' => 'delete', 'accessor_id' => 110, 'accessor_type' => ACCESSOR_TYPE_GROUP),
      )
    );
  }

  function test_save_user_objects_access()
  {
    $policy = array(
        '300' => array(
            '200' => 1,
            '210' => 1
        ),
        '301' => array(
            '200' => 0,
            '210' => 1
        )

    );

    $this->ac->save_object_access($policy, array(), ACCESSOR_TYPE_USER);

    $db_table	=& db_table_factory :: instance('sys_object_access');

    $conditions['accessor_type'] = ACCESSOR_TYPE_USER;
    $rows = $db_table->get_list($conditions, 'id', null);

    $this->assertTrue(is_array($rows));
    $this->assertEqual(count($rows), 5);

    $this->assertEqual($rows,
      array(
        array('id' => $rows[0]['id'], 'object_id' => 303, 'accessor_id' => 200, 'access' => 0, 'accessor_type' => ACCESSOR_TYPE_USER),
        array('id' => $rows[1]['id'], 'object_id' => 302, 'accessor_id' => 210, 'access' => 1, 'accessor_type' => ACCESSOR_TYPE_USER),
        array('id' => $rows[2]['id'], 'object_id' => 300, 'accessor_id' => 200, 'access' => 1, 'accessor_type' => ACCESSOR_TYPE_USER),
        array('id' => $rows[3]['id'], 'object_id' => 300, 'accessor_id' => 210, 'access' => 1, 'accessor_type' => ACCESSOR_TYPE_USER),
        array('id' => $rows[4]['id'], 'object_id' => 301, 'accessor_id' => 210, 'access' => 1, 'accessor_type' => ACCESSOR_TYPE_USER),
      )
    );
  }


  function test_save_group_objects_access()
  {
    $policy = array(
        '300' => array(
            '100' => 1,
            '110' => 1
        ),
        '301' => array(
            '100' => 0,
            '110' => 1
        )
    );

    $this->ac->save_object_access($policy, array(), ACCESSOR_TYPE_GROUP);

    $db_table	=& db_table_factory :: instance('sys_object_access');

    $conditions['accessor_type'] = ACCESSOR_TYPE_GROUP;
    $rows = $db_table->get_list($conditions, 'id', null);

    $this->assertTrue(is_array($rows));
    $this->assertEqual(count($rows), 7);

    $this->assertEqual($rows,
      array(
        array('id' => $rows[0]['id'], 'object_id' => 302, 'accessor_id' => 100, 'access' => 1, 'accessor_type' => ACCESSOR_TYPE_GROUP),
        array('id' => $rows[1]['id'], 'object_id' => 303, 'accessor_id' => 100, 'access' => 0, 'accessor_type' => ACCESSOR_TYPE_GROUP),
        array('id' => $rows[2]['id'], 'object_id' => 302, 'accessor_id' => 110, 'access' => 0, 'accessor_type' => ACCESSOR_TYPE_GROUP),
        array('id' => $rows[3]['id'], 'object_id' => 303, 'accessor_id' => 110, 'access' => 1, 'accessor_type' => ACCESSOR_TYPE_GROUP),
        array('id' => $rows[4]['id'], 'object_id' => 300, 'accessor_id' => 100, 'access' => 1, 'accessor_type' => ACCESSOR_TYPE_GROUP),
        array('id' => $rows[5]['id'], 'object_id' => 300, 'accessor_id' => 110, 'access' => 1, 'accessor_type' => ACCESSOR_TYPE_GROUP),
        array('id' => $rows[6]['id'], 'object_id' => 301, 'accessor_id' => 110, 'access' => 1, 'accessor_type' => ACCESSOR_TYPE_GROUP),
      )
    );
  }


  function test_copy_user_objects_access()
  {
    $this->ac->copy_object_access($object_id = 304, $parent_id = 300, ACCESSOR_TYPE_USER);

    $db_table	=& db_table_factory :: instance('sys_object_access');

    $conditions['accessor_type'] = ACCESSOR_TYPE_USER;
    $conditions['object_id'] = $object_id;

    $rows = $db_table->get_list($conditions, 'id', null);

    $this->assertEqual(count($rows), 2);

    $this->assertEqual($rows,
      array(
        array('id' => $rows[0]['id'], 'object_id' => 304, 'accessor_id' => 200, 'access' => 1, 'accessor_type' => ACCESSOR_TYPE_USER),
        array('id' => $rows[1]['id'], 'object_id' => 304, 'accessor_id' => 210, 'access' => 1, 'accessor_type' => ACCESSOR_TYPE_USER),
      )
    );

    $user_objects_access = $this->ac->get_object_access_by_ids(array(300, 301, 302, 303, 304), ACCESSOR_TYPE_USER);

    $this->assertEqual(sizeof($user_objects_access), 4);

    $this->assertEqual($user_objects_access[304],
      array(
        200 => 1,
        210 => 1,
      )
    );

  }

  function test_copy_group_objects_access()
  {
    $this->ac->copy_object_access($object_id = 304, $parent_id = 300, ACCESSOR_TYPE_GROUP);

    $db_table	=& db_table_factory :: instance('sys_object_access');

    $conditions['accessor_type'] = ACCESSOR_TYPE_GROUP;
    $conditions['object_id'] = $object_id;

    $rows = $db_table->get_list($conditions, 'id', null);

    $this->assertEqual(count($rows), 2);

    $this->assertEqual($rows,
      array(
        array('id' => $rows[0]['id'], 'object_id' => 304, 'accessor_id' => 100, 'access' => 1, 'accessor_type' => ACCESSOR_TYPE_GROUP),
        array('id' => $rows[1]['id'], 'object_id' => 304, 'accessor_id' => 110, 'access' => 1, 'accessor_type' => ACCESSOR_TYPE_GROUP),
      )
    );

    $group_objects_access = $this->ac->get_object_access_by_ids(array(300, 301, 302, 303, 304), ACCESSOR_TYPE_GROUP);

    $this->assertEqual(sizeof($group_objects_access), 5);

    $this->assertEqual($group_objects_access[304],
      array(
        100 => 1,
        110 => 1,
      )
    );
  }
}

?>