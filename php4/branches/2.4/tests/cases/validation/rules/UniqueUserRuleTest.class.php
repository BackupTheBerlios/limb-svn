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

    $this->db =& new SimpleDb(LimbDbPool :: getConnection());

    $this->_cleanUp();

    $this->db->insert('user', array('id' => 1, 'login' => 'vasa', 'name' => 'Vasa', 'password' => '1'));
    $this->db->insert('user', array('id' => 2, 'login' => 'sasa', 'name' => 'Sasa', 'password' => '1'));
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