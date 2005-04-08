<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: VersionedObjectMapperTest.class.php 1181 2005-03-21 10:46:55Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/data_mappers/EntityDataMapper.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');
require_once(LIMB_DIR . '/core/entity/Entity.class.php');

Mock :: generate('AbstractDataMapper', 'MockMapper');
Mock :: generate('Object', 'MockPart');

Mock :: generatePartial('LimbBaseToolkit',
                        'ToolkitEntityMapperTestVersion',
                        array('createDataMapper'));


class EntityPartStub1 extends Object
{
  var $__class_name = 'EntityPartStub1';
}

class EntityPartStub2 extends Object
{
  var $__class_name = 'EntityPartStub2';
}


class EntityMapperTest extends LimbTestCase
{
  var $db;
  var $toolkit;

  function EntityMapperTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->toolkit = new ToolkitEntityMapperTestVersion($this);
    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    Limb :: restoreToolkit();
  }

  function _cleanUp()
  {
  }

  function testLoad()
  {
    $mapper1 = new MockMapper($this);
    $mapper2 = new MockMapper($this);//will be used by default

    $mapper = new EntityDataMapper();
    $mapper->registerPartMapper('Part1', $mapper1);

    $part1 = new EntityPartStub1();
    $part2 = new EntityPartStub2();

    $entity = new Entity();
    $entity->registerPart('Part1', $part1);
    $entity->registerPart('Part2', $part2);

    $this->toolkit->expectCallCount('createDataMapper', 2);
    $this->toolkit->expectArgumentsAt(0, 'createDataMapper', array('ObjectMapper'));
    $this->toolkit->expectArgumentsAt(1, 'createDataMapper', array('EntityPartStub2Mapper'));

    $object_mapper = new MockMapper($this);

    $this->toolkit->setReturnReference('createDataMapper', $object_mapper, array('ObjectMapper'));
    $this->toolkit->setReturnReference('createDataMapper', $mapper2, array('EntityPartStub2Mapper'));

    $record = new DataSpace();
    $record->set('oid', $oid = 1000);

    $object_mapper->expectOnce('load', array($record, $entity));
    $mapper1->expectOnce('load', array($record, $part1));
    $mapper2->expectOnce('load', array($record, $part2));

    $mapper->load($record, $entity);

    $this->assertEqual($part1->get('oid'), $oid);
    $this->assertEqual($part2->get('oid'), $oid);

    $object_mapper->tally();
    $mapper1->tally();
    $mapper2->tally();
  }

  function testSave()
  {
    $mapper = new EntityDataMapper();

    $part1 = new EntityPartStub1();
    $part2 = new EntityPartStub2();

    $entity = new Entity();
    $entity->set('oid', $oid = 1000);
    $entity->registerPart('Part1', $part1);
    $entity->registerPart('Part2', $part2);

    $this->toolkit->expectCallCount('createDataMapper', 3);
    $this->toolkit->expectArgumentsAt(0, 'createDataMapper', array('ObjectMapper'));
    $this->toolkit->expectArgumentsAt(1, 'createDataMapper', array('EntityPartStub1Mapper'));
    $this->toolkit->expectArgumentsAt(2, 'createDataMapper', array('EntityPartStub2Mapper'));

    $object_mapper = new MockMapper($this);
    $mapper1 = new MockMapper($this);
    $mapper2 = new MockMapper($this);

    $this->toolkit->setReturnReference('createDataMapper', $object_mapper, array('ObjectMapper'));
    $this->toolkit->setReturnReference('createDataMapper', $mapper1, array('EntityPartStub1Mapper'));
    $this->toolkit->setReturnReference('createDataMapper', $mapper2, array('EntityPartStub2Mapper'));

    $expected_part1 = new EntityPartStub1();
    $expected_part1->set('oid', $oid);

    $expected_part2 = new EntityPartStub2();
    $expected_part2->set('oid', $oid);

    $object_mapper->expectOnce('save', array($entity));
    $mapper1->expectOnce('save', array($expected_part1));
    $mapper2->expectOnce('save', array($expected_part2));

    $mapper->save($entity);

    $this->assertEqual($part1->get('oid'), $oid);
    $this->assertEqual($part2->get('oid'), $oid);

    $object_mapper->tally();
    $mapper1->tally();
    $mapper2->tally();
  }

  function testDelete()
  {
    $mapper = new EntityDataMapper();

    $part1 = new EntityPartStub1();
    $part2 = new EntityPartStub2();

    $entity = new Entity();
    $entity->registerPart('Part1', $part1);
    $entity->registerPart('Part2', $part2);

    $this->toolkit->expectCallCount('createDataMapper', 3);
    $this->toolkit->expectArgumentsAt(0, 'createDataMapper', array('ObjectMapper'));
    $this->toolkit->expectArgumentsAt(1, 'createDataMapper', array('EntityPartStub1Mapper'));
    $this->toolkit->expectArgumentsAt(2, 'createDataMapper', array('EntityPartStub2Mapper'));

    $object_mapper = new MockMapper($this);
    $mapper1 = new MockMapper($this);
    $mapper2 = new MockMapper($this);

    $this->toolkit->setReturnReference('createDataMapper', $object_mapper, array('ObjectMapper'));
    $this->toolkit->setReturnReference('createDataMapper', $mapper1, array('EntityPartStub1Mapper'));
    $this->toolkit->setReturnReference('createDataMapper', $mapper2, array('EntityPartStub2Mapper'));

    $object_mapper->expectOnce('delete', array($entity));
    $mapper1->expectOnce('delete', array($part1));
    $mapper2->expectOnce('delete', array($part2));

    $mapper->delete($entity);

    $object_mapper->tally();
    $mapper1->tally();
    $mapper2->tally();
  }
}

?>