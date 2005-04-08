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
require_once(LIMB_DIR . '/core/db/SimpleInsertSQL.class.php');

class SimpleInsertSQLTest extends LimbTestCase
{
  var $sql;

  function SimpleInsertSQLTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->sql = new SimpleInsertSQL('test');
  }

  function testInsert()
  {
    $this->sql->addField('id', ':id');
    $this->sql->addField('title', ':title');

    $this->assertEqual($this->sql->toString(),
                       'INSERT INTO test (id,title) VALUES (:id,:title)');
  }
}
?>
