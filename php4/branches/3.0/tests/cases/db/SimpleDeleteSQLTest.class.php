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
require_once(LIMB_DIR . '/core/db/SimpleDeleteSQL.class.php');

class SimpleDeleteSQLTest extends LimbTestCase
{
  var $sql;

  function SimpleDeleteSQLTest()
  {
    parent :: LimbTestCase('simple delete sql tests');
  }

  function setUp()
  {
    $this->sql = new SimpleDeleteSQL('test');
  }

  function testDelete()
  {
    $this->assertEqual($this->sql->toString(), 'DELETE FROM test');
  }

  function testDeleteFiltered()
  {
    $this->sql->addCondition('c1=:c1:');
    $this->sql->addCondition('c2=:c2:');

    $this->assertEqual($this->sql->toString(),
                       'DELETE FROM test WHERE (c1=:c1:) AND (c2=:c2:)');
  }
}
?>
