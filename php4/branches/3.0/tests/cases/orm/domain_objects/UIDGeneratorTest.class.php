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
require_once(LIMB_DIR . '/core/UIDGenerator.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');

class UIDGeneratorTest extends LimbTestCase
{
  var $generator;
  var $db;

  function UIDGeneratorTest()
  {
    parent :: LimbTestCase('UID generator tests');
  }

  function setUp()
  {
    $this->db =& new SimpleDb(LimbDbPool :: getConnection());

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_uid');
  }

  function testGenerateFirst()
  {
    $uid = UIDGenerator :: next();
    $this->assertEqual($uid, 1);
  }

  function testGenerate()
  {
    $this->db->insert('sys_uid', array('id' => 1000));
    $uid = UIDGenerator :: next();
    $this->assertEqual($uid, 1001);
  }

  function testCurrent()
  {
    $this->assertFalse(UIDGenerator :: current());

    UIDGenerator :: next();
    UIDGenerator :: next();
    UIDGenerator :: next();

    $this->assertEqual(UIDGenerator :: current(), 3);
  }
}

?>