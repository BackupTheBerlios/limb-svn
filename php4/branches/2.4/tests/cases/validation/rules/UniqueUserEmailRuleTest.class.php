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
require_once(LIMB_DIR . '/core/validators/rules/UniqueUserEmailRule.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbTable.class.php');

class UniqueEmailUserRuleTest extends SingleFieldRuleTestCase
{
  var $db = null;

  function UniqueEmailUserRuleTest()
  {
    parent :: SingleFieldRuleTestCase('unique email user rule test');
  }

  function setUp()
  {
    parent :: setUp();

    $this->db =& new SimpleDb(LimbDbPool :: getConnection());

    $this->_cleanUp();

    $this->db->delete('user');
    $this->db->insert('user', array('id' => 1, 'login' => 'vasa', 'name' => 'Vasa',' email' => '1@1.1', 'password' => '1'));
    $this->db->insert('user', array('id' => 2, 'login' => 'sasa', 'name' => 'Sasa', 'email' => '2@2.2', 'password' => '1'));
  }

  function tearDown()
  {
    parent :: tearDown();

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('user');
  }

  function testUniqueUserEmailRuleCorrect()
  {
    $this->validator->addRule(new UniqueUserEmailRule('test'));

    $data = new Dataspace();
    $data->set('test', '3@3.3');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testUniqueUserEmailRuleError()
  {
    $this->validator->addRule(new UniqueUserEmailRule('test'));

    $data = new Dataspace();
    $data->set('test', '2@2.2');

    $this->ErrorList->expectOnce('addError', array('validation',
                                                   'ERROR_DUPLICATE_USER',
                                                   array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }


  function testUniqueUserEmailRuleCorrectEdit()
  {
    $this->validator->addRule(new UniqueUserEmailRule('test', '2@2.2'));

    $data = new Dataspace();
    $data->set('test', '2@2.2');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }
}

?>