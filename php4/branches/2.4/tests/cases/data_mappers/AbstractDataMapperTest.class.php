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
require_once(LIMB_DIR . '/core/data_mappers/AbstractDataMapper.class.php');
require_once(LIMB_DIR . '/core/DomainObject.class.php');
require_once(WACT_ROOT . '/iterator/arraydataset.inc.php');

Mock :: generate('DomainObject');

class AbstractDataMapperTestVersion extends AbstractDataMapper{}

Mock :: generatePartial('AbstractDataMapperTestVersion',
                        'AbstractDataMapperMock',
                        array('insert',
                              'update'));

class AbstractDataMapperTest extends LimbTestCase
{
  var $object;

  function AbstractDataMapperTest()
  {
    parent :: LimbTestCase('abstract mapper test');
  }

  function setUp()
  {
    $this->object = new MockDomainObject($this);
  }

  function tearDown()
  {
    $this->object->tally();
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