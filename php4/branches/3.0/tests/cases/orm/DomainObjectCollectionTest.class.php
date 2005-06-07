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
require_once(LIMB_DIR . '/core/orm/DomainObjectCollection.class.php');
require_once(WACT_ROOT . '/iterator/arraydataset.inc.php');

class DomainObjectCollectionTest extends LimbTestCase
{
  function DomainObjectCollectionTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function testRewind()
  {
    $obj1 = new Object();
    $obj1->set('id', 1);

    $obj2 = new Object();
    $obj2->set('id', 2);

    $arr = array($obj1, $obj2);
    $col = new DomainObjectCollection($arr);

    $col->rewind();

    $this->assertEqual($col->current(), $obj1);

    $col->next();
    $this->assertEqual($col->current(), $obj2);

    $col->next();
    $this->assertFalse($col->valid());
  }

  function testAdd()
  {
    $obj1 = new Object();
    $obj1->set('id', 1);

    $obj2 = new Object();
    $obj2->set('id', 2);

    $arr = array($obj1);
    $col = new DomainObjectCollection($arr);

    $col->add($obj2);

    $col->rewind();

    $this->assertEqual($col->current(), $obj1);

    $col->next();
    $this->assertEqual($col->current(), $obj2);

    $col->next();
    $this->assertFalse($col->valid());
  }
}

?>
