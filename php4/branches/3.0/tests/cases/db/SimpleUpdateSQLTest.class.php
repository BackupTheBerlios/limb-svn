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
require_once(LIMB_DIR . '/core/db/SimpleUpdateSQL.class.php');

class SimpleUpdateSQLTest extends LimbTestCase
{
  var $sql;

  function SimpleUpdateSQLTest()
  {
    parent :: LimbTestCase('simple update sql tests');
  }

  function setUp()
  {
    $this->sql = new SimpleUpdateSQL('test');
  }

  function testUpdate()
  {
    $this->sql->addField('id=:id:');
    $this->sql->addField('title=:title:');

    $this->assertEqual($this->sql->toString(),
                       "UPDATE test SET id=:id:,title=:title:");
  }

  function testUpdateFiltered()
  {
    $this->sql->addField('id=:id:');
    $this->sql->addField('title=:title:');

    $this->sql->addCondition('c1=:c1:');
    $this->sql->addCondition('c2=:c2:');

    $this->assertEqual($this->sql->toString(),
                       "UPDATE test SET id=:id:,title=:title: WHERE (c1=:c1:) AND (c2=:c2:)");
  }
}
?>
