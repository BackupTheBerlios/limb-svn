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
require_once(LIMB_DIR . '/core/lib/util/dataspace.class.php');
require_once(LIMB_DIR . '/core/lib/validators/rules/unique_user_email_rule.class.php');
require_once(LIMB_DIR . '/core/lib/db/db_table.class.php');
require_once(LIMB_DIR . '/tests/cases/validation/rules/single_field_rule_test.class.php');

class unique_email_user_rule_test extends single_field_rule_test
{
  var $db = null;

  function setUp()
  {
    parent :: setUp();

    $this->db = db_factory :: instance();

    $this->db->sql_delete('user');
    $this->db->sql_delete('sys_site_object');

    $this->db->sql_insert('sys_site_object', array('id' => 1, 'identifier' => 'vasa', 'class_id' => '1', 'current_version' => '1'));
    $this->db->sql_insert('sys_site_object', array('id' => 2, 'identifier' => 'sasa', 'class_id' => '1', 'current_version' => '1'));
    $this->db->sql_insert('user', array('id' => 1, 'name' => 'Vasa',' email' => '1@1.1', 'password' => '1', 'version' => '1', 'object_id' => '1'));
    $this->db->sql_insert('user', array('id' => 2, 'name' => 'Sasa', 'email' => '2@2.2', 'password' => '1', 'version' => '1', 'object_id' => '2'));
    $this->db->sql_insert('user', array('id' => 3, 'name' => 'Sasa', 'email' => '3@3.3', 'password' => '1', 'version' => '2', 'object_id' => '2'));
  }

  function tearDown()
  {
    parent :: tearDown();

    $this->db->sql_delete('user');
    $this->db->sql_delete('sys_site_object');
  }

  function test_unique_user_email_rule_correct()
  {
    $this->validator->add_rule(new unique_user_email_rule('test'));

    $data =& new dataspace();
    $data->set('test', '3@3.3');

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }

  function test_unique_user_email_rule_error()
  {
    $this->validator->add_rule(new unique_user_email_rule('test'));

    $data =& new dataspace();
    $data->set('test', '2@2.2');

    $this->error_list->expectOnce('add_error', array('test', strings :: get('error_duplicate_user', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->is_valid());
  }


  function test_unique_user_email_rule_correct_edit()
  {
    $this->validator->add_rule(new unique_user_email_rule('test', '2@2.2'));

    $data =& new dataspace();
    $data->set('test', '2@2.2');

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }
}

?>