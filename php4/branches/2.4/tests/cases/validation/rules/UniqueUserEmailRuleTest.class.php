<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/SingleFieldRuleTest.class.php');
require_once(LIMB_DIR . '/class/core/Dataspace.class.php');
require_once(LIMB_DIR . '/class/validators/rules/UniqueUserEmailRule.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbTable.class.php');

class UniqueEmailUserRuleTest extends SingleFieldRuleTest
{
  var $db = null;

  function setUp()
  {
    parent :: setUp();

    $this->db =& DbFactory :: instance();

    $this->db->sqlDelete('user');
    $this->db->sqlDelete('sys_site_object');

    $this->db->sqlInsert('sys_site_object', array('id' => 1, 'identifier' => 'vasa', 'class_id' => '1', 'current_version' => '1'));
    $this->db->sqlInsert('sys_site_object', array('id' => 2, 'identifier' => 'sasa', 'class_id' => '1', 'current_version' => '1'));
    $this->db->sqlInsert('user', array('id' => 1, 'name' => 'Vasa',' email' => '1@1.1', 'password' => '1', 'version' => '1', 'object_id' => '1'));
    $this->db->sqlInsert('user', array('id' => 2, 'name' => 'Sasa', 'email' => '2@2.2', 'password' => '1', 'version' => '1', 'object_id' => '2'));
    $this->db->sqlInsert('user', array('id' => 3, 'name' => 'Sasa', 'email' => '3@3.3', 'password' => '1', 'version' => '2', 'object_id' => '2'));
  }

  function tearDown()
  {
    parent :: tearDown();

    $this->db->sqlDelete('user');
    $this->db->sqlDelete('sys_site_object');
  }

  function testUniqueUserEmailRuleCorrect()
  {
    $this->validator->addRule(new UniqueUserEmailRule('test'));

    $data = new Dataspace();
    $data->set('test', '3@3.3');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testUniqueUserEmailRuleError()
  {
    $this->validator->addRule(new UniqueUserEmailRule('test'));

    $data = new Dataspace();
    $data->set('test', '2@2.2');

    $this->error_list->expectOnce('addError', array('test', Strings :: get('error_duplicate_user', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }


  function testUniqueUserEmailRuleCorrectEdit()
  {
    $this->validator->addRule(new UniqueUserEmailRule('test', '2@2.2'));

    $data = new Dataspace();
    $data->set('test', '2@2.2');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }
}

?>