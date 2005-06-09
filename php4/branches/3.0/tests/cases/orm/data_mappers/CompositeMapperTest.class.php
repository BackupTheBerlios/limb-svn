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
require_once(LIMB_DIR . '/core/orm/data_mappers/AbstractDataMapper.class.php');
require_once(LIMB_DIR . '/core/orm/data_mappers/CompositeMapper.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');

Mock :: generate('AbstractDataMapper');
Mock :: generate('Object');

class AbstractDataMapperForCompositeTestVersion extends MockAbstractDataMapper
{
  function setCounter(&$counter)
  {
    $this->counter =& $counter;
  }

  function load(&$record, &$object)
  {
    $this->call_order = $this->counter++;
    parent :: load($record, $object);
  }

  function update(&$object)
  {
    $this->call_order = $this->counter++;
    parent :: update($object);
  }

  function insert(&$object)
  {
    $this->call_order = $this->counter++;
    parent :: insert($object);
  }

  function delete(&$object)
  {
    $this->call_order = $this->counter++;
    parent :: delete($object);
  }

}

class CompositeMapperTest extends LimbTestCase
{
  var $object;

  function CompositeMapperTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->object = new Object();
  }

  function tearDown()
  {
  }

  function testLoad()
  {
    $m1 = new AbstractDataMapperForCompositeTestVersion($this);
    $m2 = new AbstractDataMapperForCompositeTestVersion($this);

    $counter = 1;
    $m1->setCounter($counter);
    $m2->setCounter($counter);

    $record = array('record emulation');

    $mapper = new CompositeMapper();
    $mapper->registerMapper($m1);
    $mapper->registerMapper($m2);

    $m1->expectOnce('load', array($record,  $this->object));
    $m2->expectOnce('load', array($record,  $this->object));

    $mapper->load($record, $this->object);

    $this->assertEqual($m1->call_order, 1);
    $this->assertEqual($m2->call_order, 2);

    $m1->tally();
    $m2->tally();
  }

  function testUpdate()
  {
    $m1 = new AbstractDataMapperForCompositeTestVersion($this);
    $m2 = new AbstractDataMapperForCompositeTestVersion($this);

    $counter = 1;
    $m1->setCounter($counter);
    $m2->setCounter($counter);

    $mapper = new CompositeMapper();
    $mapper->registerMapper($m1);
    $mapper->registerMapper($m2);

    $m1->expectOnce('update', array($this->object));
    $m2->expectOnce('update', array($this->object));

    $mapper->update($this->object);

    $this->assertEqual($m1->call_order, 1);
    $this->assertEqual($m2->call_order, 2);

    $m1->tally();
    $m2->tally();
  }

  function testInsert()
  {
    $m1 = new AbstractDataMapperForCompositeTestVersion($this);
    $m2 = new AbstractDataMapperForCompositeTestVersion($this);

    $counter = 1;
    $m1->setCounter($counter);
    $m2->setCounter($counter);

    $mapper = new CompositeMapper();
    $mapper->registerMapper($m1);
    $mapper->registerMapper($m2);

    $m1->expectOnce('insert', array($this->object));
    $m2->expectOnce('insert', array($this->object));

    $mapper->insert($this->object);

    $this->assertEqual($m1->call_order, 1);
    $this->assertEqual($m2->call_order, 2);

    $m1->tally();
    $m2->tally();
  }

  function testDelete()
  {
    $m1 = new AbstractDataMapperForCompositeTestVersion($this);
    $m2 = new AbstractDataMapperForCompositeTestVersion($this);

    $counter = 1;
    $m1->setCounter($counter);
    $m2->setCounter($counter);

    $mapper = new CompositeMapper();
    $mapper->registerMapper($m1);
    $mapper->registerMapper($m2);

    $m1->expectOnce('delete', array($this->object));
    $m2->expectOnce('delete', array($this->object));

    $mapper->delete($this->object);

    $this->assertEqual($m1->call_order, 1);
    $this->assertEqual($m2->call_order, 2);

    $m1->tally();
    $m2->tally();
  }

}

?>