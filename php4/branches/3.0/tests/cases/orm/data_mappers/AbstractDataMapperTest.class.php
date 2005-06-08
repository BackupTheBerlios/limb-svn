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
require_once(LIMB_DIR . '/core/Object.class.php');
require_once(LIMB_DIR . '/core/UnitOfWork.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(WACT_ROOT . '/iterator/arraydataset.inc.php');

Mock :: generate('UnitOfWork');
Mock :: generate('LimbBaseToolkit', 'MockToolkit');

class AbstractDataMapperTestVersion extends AbstractDataMapper{}

Mock :: generatePartial('AbstractDataMapperTestVersion',
                        'AbstractDataMapperMock',
                        array('insert',
                              'update'));

class AbstractDataMapperTest extends LimbTestCase
{
  var $object;
  var $toolkit;
  var $uow;

  function AbstractDataMapperTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->object = new Object();

    $this->toolkit =& Limb :: registerToolkit(new MockToolkit($this));

    $this->uow = new MockUnitOfWork($this);
    $this->toolkit->setReturnReference('getUOW', $this->uow);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->uow->tally();

    Limb :: restoreToolkit();
  }

  function testInsertNewObject()
  {
    $mapper = new AbstractDataMapperMock($this);

    $this->uow->expectNever('isDirty');

    $this->uow->expectOnce('isNew', array($this->object));
    $this->uow->setReturnValue('isNew', true);

    $mapper->expectNever('update');
    $mapper->expectOnce('insert', array($this->object));

    $mapper->save($this->object);

    $mapper->tally();
  }

  function testUpdateDirtyObject()
  {
    $mapper = new AbstractDataMapperMock($this);

    $this->uow->expectOnce('isDirty', array($this->object));
    $this->uow->setReturnValue('isDirty', true);

    $this->uow->expectOnce('isNew', array($this->object));
    $this->uow->setReturnValue('isNew', false);

    $mapper->expectNever('insert');
    $mapper->expectOnce('update', array($this->object));

    $mapper->save($this->object);

    $mapper->tally();
  }

  function testDontUpdateUnchangedObject()
  {
    $mapper = new AbstractDataMapperMock($this);

    $this->uow->expectOnce('isNew', array($this->object));
    $this->uow->setReturnValue('isNew', false);

    $this->uow->expectOnce('isDirty', array($this->object));
    $this->uow->setReturnValue('isDirty', false);

    $mapper->expectNever('update');
    $mapper->expectNever('insert');

    $mapper->save($this->object);

    $mapper->tally();
  }
}

?>