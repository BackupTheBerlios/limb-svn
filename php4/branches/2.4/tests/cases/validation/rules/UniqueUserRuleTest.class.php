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
require_once(WACT_ROOT . '/../tests/cases/validation/rules/singlefield.inc.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(LIMB_DIR . '/core/validators/rules/UniqueUserRule.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbTable.class.php');

class UniqueUserRuleTest extends SingleFieldRuleTestCase
{
  var $db = null;

  function UniqueUserRuleTest()
  {
    parent :: SingleFieldRuleTestCase('unique user rule test');
  }

  function setUp()
  {
    parent :: setUp();

    $this->db =& LimbDbPool :: getConnection();
    $this->db->sqlDelete('user');
    $this->db->sqlDelete('sys_site_object');

    $this->db->sqlInsert('sys_site_object', array('id' => 1, 'identifier' => 'vasa', 'class_id' => '1', 'current_version' => '1'));
    $this->db->sqlInsert('sys_site_object', array('id' => 2, 'identifier' => 'sasa', 'class_id' => '1', 'current_version' => '1'));
    $this->db->sqlInsert('user', array('id' => 1, 'name' => 'Vasa', 'password' => '1', 'version' => '1', 'object_id' => '1'));
    $this->db->sqlInsert('user', array('id' => 2, 'name' => 'Sasa', 'password' => '1', 'version' => '1', 'object_id' => '2'));
  }

  function tearDown()
  {
    parent :: tearDown();

    $this->db->sqlDelete('user');
    $this->db->sqlDelete('sys_site_object');
  }

  function testUniqueUserRuleCorrect()
  {
    $this->validator->addRule(new UniqueUserRule('test'));

    $data = new Dataspace();
    $data->set('test', 'maso');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testUniqueUserRuleError()
  {
    $this->validator->addRule(new UniqueUserRule('test'));

    $data = new Dataspace();
    $data->set('test', 'vasa');

    $this->ErrorList->expectOnce('addError', array('validation', 'ERROR_DUPLICATE_USER', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testUniqueUserRuleCorrectEdit()
  {
    $this->validator->addRule(new UniqueUserRule('test', 'maso'));

    $data = new Dataspace();
    $data->set('test', 'maso');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }
}

?>