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
require_once(LIMB_DIR . '/class/data_mappers/AbstractDataMapper.class.php');
require_once(LIMB_DIR . '/class/DomainObject.class.php');
require_once(LIMB_DIR . '/class/finders/DataFinder.interface.php');

Mock :: generate('DomainObject');
Mock :: generate('DataFinder');

class AbstractDataMapperTestVersion extends AbstractDataMapper{}

Mock :: generatePartial('AbstractDataMapperTestVersion',
                        'AbstractDataMapperMock',
                        array('insert',
                              'update',
                              '_createDomainObject',
                              '_doLoad',
                              '_getFinder'));

class AbstractDataMapperTest extends LimbTestCase
{
  var $object;
  var $finder;

  function AbstractDataMapperTest()
  {
    parent :: LimbTestCase('abstract mapper test');
  }

  function setUp()
  {
    $this->object = new MockDomainObject($this);
    $this->finder = new MockDataFinder($this);
  }

  function tearDown()
  {
    $this->object->tally();
    $this->finder->tally();
  }

  function testFindByIdNull()
  {
    $mapper = new AbstractDataMapperMock($this);
    $mapper->setReturnReference('_getFinder', $this->finder);

    $this->finder->expectOnce('findById', array($id = 100));
    $this->finder->setReturnValue('findById', array(), array($id = 100));

    $mapper->expectNever('_createDomainObject');
    $mapper->expectNever('_doLoad');

    $this->assertNull($mapper->findById($id));

    $mapper->tally();
  }

  function testFindById()
  {
    $mapper = new AbstractDataMapperMock($this);
    $mapper->setReturnReference('_getFinder', $this->finder);

    $this->finder->expectOnce('findById', array($id = 100));
    $this->finder->setReturnValue('findById', $result_set = array('whatever'), array($id = 100));

    $mapper->expectOnce('_createDomainObject');
    $mapper->setReturnReference('_createDomainObject', $object = new DomainObject());

    $mapper->expectOnce('_doLoad', array($result_set, $object));

    $this->assertTrue($mapper->findById($id) === $object);

    $mapper->tally();
  }

  function testSaveInsert()
  {
    $mapper = new AbstractDataMapperMock($this);

    $mapper->expectOnce('insert', array(new IsAExpectation('MockDomainObject')));

    $this->object->expectOnce('getId');

    $mapper->save($this->object);

    $mapper->tally();
  }

  function testSaveUpdate()
  {
    $mapper = new AbstractDataMapperMock($this);

    $mapper->expectOnce('update', array(new IsAExpectation('MockDomainObject')));

    $this->object->expectOnce('getId');
    $this->object->setReturnValue('getId', 10);

    $mapper->save($this->object);

    $mapper->tally();
  }
}

?>