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
require_once(LIMB_DIR . '/core/lib/util/dataspace.class.php');
require_once(LIMB_DIR . '/core/lib/validators/rules/user_old_password_rule.class.php');
require_once(LIMB_DIR . '/tests/cases/validation/rules/single_field_rule_test.class.php');

class user_old_password_rule_test extends single_field_rule_test
{
  function setUp()
  {
    parent :: setUp();

    $user =& user :: instance();

    $user->_set_login('admin');
    $user->_set_password('66d4aaa5ea177ac32c69946de3731ec0');
    $user->_set_node_id(1);
    $user->_set_is_logged_in();
  }

  function tearDown()
  {
    parent :: tearDown();

    $user =& user :: instance();
    $user->logout();
  }

  function test_user_old_password_rule_correct()
  {
    $this->validator->add_rule(new user_old_password_rule('old_password'));

    $data =& new dataspace();
    $data->set('old_password', 'test');

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }

  function test_user_old_password_rule_wrong_password()
  {
    $this->validator->add_rule(new user_old_password_rule('old_password'));

    $data =& new dataspace();
    $data->set('old_password', 'wrong_pass');

    $this->error_list->expectOnce('add_error', array('old_password', 'WRONG_OLD_PASSWORD', array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->is_valid());
  }

  function test_user_old_password_rule_empty_password()
  {
    $this->validator->add_rule(new user_old_password_rule('old_password'));

    $data =& new dataspace();
    $data->set('old_password', '');

    $this->error_list->expectOnce('add_error', array('old_password', 'WRONG_OLD_PASSWORD', array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->is_valid());
  }
}

?>