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
require_once(LIMB_DIR . '/tests/cases/db_test.class.php');
require_once(LIMB_DIR . '/core/model/access_policy.class.php');

class access_templates_test extends db_test
{
  var $dump_file = 'access_policy_load.sql';

  var $ac = null;

  function setUp()
  {
    parent :: setUp();

    $this->ac =& access_policy :: instance();
  }

  function test_load_user_access_templates()
  {
    $template = $this->ac->get_action_access_templates($controller_id = 11, ACCESSOR_TYPE_USER);

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

  function test_load_group_access_templates()
  {
    $template = $this->ac->get_action_access_templates($controller_id = 10, ACCESSOR_TYPE_GROUP);

    $this->assertEqual(sizeof($template), 1);

    $this->assertEqual($template,
      array(
        'create' => array(
            100 => 1,
            110 => 1,
        ),
      )
    );

    $template = $this->ac->get_action_access_templates($controller_id = 11, ACCESSOR_TYPE_GROUP);

    $this->assertEqual(sizeof($template), 1);

    $this->assertEqual($template,
      array(
        'create' => array(
            100 => 1,
            110 => 1,
        ),
      )
    );
  }

  function test_save_user_actions_access_template()
  {
    $template = array(
        'create' => array(
            200 => 1,
            210 => 0
        ),
        'publish' => array(
            200 => 1,
            210 => 1
        )
    );

    $this->ac->save_action_access_template($controller_id = 11, $template, ACCESSOR_TYPE_USER);

    $db_table	=& db_table_factory :: instance('sys_access_template');
    $templates_rows = $db_table->get_list('', 'id', null);

    $items_db_table	=& db_table_factory :: instance('sys_access_template_item');
    $items_rows = $items_db_table->get_list('', 'id', null);

    $this->assertTrue(is_array($templates_rows));
    $this->assertEqual(count($templates_rows), 5);

    $this->assertTrue(is_array($items_rows));
    $this->assertEqual(count($items_rows), 9);

    $this->assertEqual($templates_rows,
      array(
        array('id' => $templates_rows[0]['id'], 'controller_id' => 10, 'action_name' => 'create', 'accessor_type' => ACCESSOR_TYPE_GROUP),
        array('id' => $templates_rows[1]['id'], 'controller_id' => 11, 'action_name' => 'create', 'accessor_type' => ACCESSOR_TYPE_GROUP),
        array('id' => $templates_rows[2]['id'], 'controller_id' => 12, 'action_name' => 'create', 'accessor_type' => ACCESSOR_TYPE_USER),
        array('id' => $templates_rows[3]['id'], 'controller_id' => 11, 'action_name' => 'create', 'accessor_type' => ACCESSOR_TYPE_USER),
        array('id' => $templates_rows[4]['id'], 'controller_id' => 11, 'action_name' => 'publish', 'accessor_type' => ACCESSOR_TYPE_USER),
      )
    );

    $record = reset($items_rows);
    $this->assertEqual($record['accessor_id'], 100);
    $this->assertEqual($record['access'], 1);

    $record = end($items_rows);
    $this->assertEqual($record['accessor_id'], 210);
    $this->assertEqual($record['access'], 1);
  }
}
?>