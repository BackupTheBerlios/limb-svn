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
require_once(dirname(__FILE__) . '/../../../access_policy.class.php');

class access_templates_test extends LimbTestCase
{
  var $ac = null;

  function setUp()
  {
    $this->_clean_up();
    $this->ac = access_policy :: instance();
  }

  function tearDown()
  {
    $this->_clean_up();
  }

  function _clean_up()
  {
    $db = db_factory :: instance();
    $db->sql_delete('sys_action_access_template');
    $db->sql_delete('sys_action_access_template_item');

    $db->sql_delete('sys_action_access');
    $db->sql_delete('sys_object_access');
  }

  function test_get_access_templates1()
  {
    $db_table = db_table_factory :: create('sys_action_access_template');
    $item_db_table = db_table_factory :: create('sys_action_access_template_item');

    $db_table->insert(array('id'=> 1, 'action_name' => 'create', 'class_id' => 11, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 2, 'action_name' => 'publish', 'class_id' => 11, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 3, 'action_name' => 'edit', 'class_id' => 11, 'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP));

    $item_db_table->insert(array('template_id' => 1, 'accessor_id' => 200, 'access' => 1));
    $item_db_table->insert(array('template_id' => 1, 'accessor_id' => 210, 'access' => 1));
    $item_db_table->insert(array('template_id' => 2, 'accessor_id' => 200, 'access' => 1));
    $item_db_table->insert(array('template_id' => 2, 'accessor_id' => 210, 'access' => 0));
    $item_db_table->insert(array('template_id' => 3, 'accessor_id' => 200, 'access' => 1));

    $template = $this->ac->get_access_templates($class_id = 11, access_policy :: ACCESSOR_TYPE_USER);

    $this->assertEqual(sizeof($template), 2);

    $this->assertEqual($template,
      array(
        'create' => array(
            200 => 1,
            210 => 1,
        ),
        'publish' => array(
            200 => 1,
            210 => 0,
        ),
      )
    );
  }

  function test_get_actions_access()
  {
    $db_table = db_table_factory :: create('sys_action_access');
    $db_table->insert(array('id'=> 1, 'class_id' => 11, 'action_name' => 'create', 'accessor_id' => 10, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 2, 'class_id' => 11, 'action_name' => 'edit', 'accessor_id' => 10, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 3, 'class_id' => 11, 'action_name' => 'publish', 'accessor_id' => 20, 'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP));

    $actions = $this->ac->get_actions_access(11, access_policy :: ACCESSOR_TYPE_USER);
    $this->assertEqual($actions, array(10 => array('create' => 1, 'edit' => 1)));

    $actions = $this->ac->get_actions_access(11, access_policy :: ACCESSOR_TYPE_GROUP);
    $this->assertEqual($actions, array(20 => array('publish' => 1)));
  }

  function test_get_objects_access_by_ids()
  {
    $db_table = db_table_factory :: create('sys_object_access');

    $db_table->insert(array('id'=> 1, 'object_id' => 11, 'access' => 1, 'accessor_id' => 10, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 2, 'object_id' => 11, 'access' => 1, 'accessor_id' => 11, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 3, 'object_id' => 12, 'access' => 1, 'accessor_id' => 10, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 4, 'object_id' => 13, 'access' => 0, 'accessor_id' => 10, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 5, 'object_id' => 13, 'access' => 1, 'accessor_id' => 20, 'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP));

    $access_records = $this->ac->get_objects_access_by_ids(array(11, 12, 13, 14), access_policy :: ACCESSOR_TYPE_USER);

    $this->assertEqual($access_records,
      array(
        '11' => array(
          10 => 1,
          11 => 1,
        ),
        '12' => array(
          10 => 1,
        ),
        '13' => array(
          10 => 0,
        ),
      )
    );

    $access_records = $this->ac->get_objects_access_by_ids(array(11, 12, 13, 14), access_policy :: ACCESSOR_TYPE_GROUP);

    $this->assertEqual($access_records,
      array(
        '13' => array(
          20 => 1,
        ),
      )
    );
  }

  function test_save_access_templates()
  {
    $db_table = db_table_factory :: create('sys_action_access_template');
    $items_db_table = db_table_factory :: create('sys_action_access_template_item');

    //garbage
    $db_table->insert(array('id'=> 1, 'action_name' => 'edit', 'class_id' => 11, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 2, 'action_name' => 'create', 'class_id' => 11, 'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP));

    $template = array(
        'create' => array(
            200 => 1,
            210 => 0,
        ),
        'publish' => array(
            200 => 1,
            210 => 1,
        )
    );

    $this->ac->save_access_templates($class_id = 11, $template, access_policy :: ACCESSOR_TYPE_USER);

    $templates_rows = $db_table->get_list('', 'id', null);
    $items_rows = $items_db_table->get_list('', 'id', null);

    $this->assertTrue(is_array($templates_rows));
    $this->assertEqual(count($templates_rows), 3);

    $this->assertTrue(is_array($items_rows));
    $this->assertEqual(count($items_rows), 4);

    $this->assertEqual($templates_rows,
      array(
        array('id' => $templates_rows[0]['id'], 'action_name' => 'create', 'class_id' => 11, 'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP),
        array('id' => $templates_rows[1]['id'], 'action_name' => 'create', 'class_id' => 11, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER),
        array('id' => $templates_rows[2]['id'], 'action_name' => 'publish', 'class_id' => 11, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER),
      )
    );

    $record = reset($items_rows);
    $this->assertEqual($record['accessor_id'], 200);
    $this->assertEqual($record['access'], 1);

    $record = end($items_rows);
    $this->assertEqual($record['accessor_id'], 210);
    $this->assertEqual($record['access'], 1);
  }

  function test_save_actions_access()
  {
    $db_table = db_table_factory :: create('sys_action_access');

    //garbage
    $db_table->insert(array('id'=> 1, 'class_id' => 11, 'action_name' => 'create', 'accessor_id' => 10, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 2, 'class_id' => 11, 'action_name' => 'edit', 'accessor_id' => 20, 'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP));

    $access = array(
      10 => array(
        'edit' => 1,
        'publish' => 1,
      ),
      11 => array(
        'edit' => 1,
        'publish' => 0,
      ),
    );

    $this->ac->save_actions_access(11, $access, access_policy :: ACCESSOR_TYPE_USER);

    $access_rows = $db_table->get_list('', 'id', null);
    $this->assertEqual($access_rows,
      array(
        array('id' => $access_rows[0]['id'], 'accessor_id' => 20, 'action_name' => 'edit', 'class_id' => 11, 'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP),
        array('id' => $access_rows[1]['id'], 'accessor_id' => 10, 'action_name' => 'edit', 'class_id' => 11, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER),
        array('id' => $access_rows[2]['id'], 'accessor_id' => 10, 'action_name' => 'publish', 'class_id' => 11, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER),
        array('id' => $access_rows[3]['id'], 'accessor_id' => 11, 'action_name' => 'edit', 'class_id' => 11, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER),
      )
    );
  }

  function test_save_objects_access()
  {
    $db_table = db_table_factory :: create('sys_object_access');

    //garbage
    $db_table->insert(array('id'=> 1, 'object_id' => 10, 'access' => 1, 'accessor_id' => 100, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 2, 'object_id' => 11, 'access' => 1, 'accessor_id' => 110, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 3, 'object_id' => 10, 'access' => 1, 'accessor_id' => 200, 'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP));

    $access = array(
      10 => array(
        100 => 1,
        110 => 1,
      ),
      11 => array(
        100 => 1,
        110 => 0,
      ),
    );

    $this->ac->save_objects_access($access, access_policy :: ACCESSOR_TYPE_USER, array(100, 110));

    $access_rows = $db_table->get_list('', 'id', null);
    $this->assertEqual($access_rows,
      array(
        array('id' => $access_rows[0]['id'], 'object_id' => 10, 'access' => 1, 'accessor_id' => 200, 'accessor_type' => access_policy :: ACCESSOR_TYPE_GROUP),
        array('id' => $access_rows[1]['id'], 'object_id' => 10, 'access' => 1, 'accessor_id' => 100, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER),
        array('id' => $access_rows[2]['id'], 'object_id' => 10, 'access' => 1, 'accessor_id' => 110, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER),
        array('id' => $access_rows[3]['id'], 'object_id' => 11, 'access' => 1, 'accessor_id' => 100, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER),
      )
    );
  }

  function test_save_object_access_no_accessor_ids_limit()
  {
    $db_table = db_table_factory :: create('sys_object_access');

    //garbage
    $db_table->insert(array('id'=> 1, 'object_id' => 10, 'access' => 1, 'accessor_id' => 100, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 2, 'object_id' => 11, 'access' => 1, 'accessor_id' => 110, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 3, 'object_id' => 10, 'access' => 1, 'accessor_id' => 200, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER));

    $access = array(
      10 => array(
        100 => 1,
        110 => 1,
      ),
      11 => array(
        100 => 1,
        110 => 0,
      ),
    );

    $this->ac->save_objects_access($access, access_policy :: ACCESSOR_TYPE_USER);

    $access_rows = $db_table->get_list('', 'id', null);
    $this->assertEqual($access_rows,
      array(
        array('id' => $access_rows[0]['id'], 'object_id' => 10, 'access' => 1, 'accessor_id' => 100, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER),
        array('id' => $access_rows[1]['id'], 'object_id' => 10, 'access' => 1, 'accessor_id' => 110, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER),
        array('id' => $access_rows[2]['id'], 'object_id' => 11, 'access' => 1, 'accessor_id' => 100, 'accessor_type' => access_policy :: ACCESSOR_TYPE_USER),
      )
    );
  }
}
?>