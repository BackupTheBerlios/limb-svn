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
require_once(LIMB_DIR . '/class/Object.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');

Mock :: generate('Dataspace');
Mock :: generatePartial('Object',
                        'ObjectTestVersion',
                        array('_createDataspace'));

class ObjectTest extends LimbTestCase
{
  var $object;
  var $dataspace;

  function setUp()
  {
    $this->dataspace = new MockDataspace($this);

    $this->object = new ObjectTestVersion($this);
    $this->object->setReturnReference('_createDataspace', $this->dataspace);
    $this->object->Object();
  }

  function tearDown()
  {
    $this->dataspace->tally();
  }

  function testImport()
  {
    $values = array('test');

    $this->dataspace->expectOnce('import', array($values));

    $this->object->import($values);
  }

  function testMerge()
  {
    $values = array('test');

    $this->dataspace->expectOnce('merge', array($values));

    $this->object->merge($values);
  }

  function testExport()
  {
    $values = array('test');

    $this->dataspace->setReturnValue('export', $values);

    $this->assertEqual($this->object->export(), $values);
  }

  function testHasAttributeTrue1()
  {
    $values = array('test');

    $this->dataspace->setReturnValue('get', 1, array($property = 'test'));

    $this->assertTrue($this->object->hasAttribute($property));
  }

  function testHasAttributeTrue2()
  {
    $values = array('test');

    $this->dataspace->setReturnValue('get', 0, array($property = 'test'));

    $this->assertTrue($this->object->hasAttribute($property));
  }

  function testHasAttributeTrue3()
  {
    $values = array('test');

    $this->dataspace->setReturnValue('get', '', array($property = 'test'));

    $this->assertTrue($this->object->hasAttribute($property));
  }

  function testHasAttributeFalse()
  {
    $values = array('test');

    $this->dataspace->setReturnValue('get', null, array($property = 'test'));

    $this->assertFalse($this->object->hasAttribute($property));
  }

  function testGet()
  {
    $value = 'test';

    $this->dataspace->setReturnValue('get', $value, array($property = 'test', null));

    $this->assertEqual($this->object->get($property), $value);
  }

  function testSet()
  {
    $property = 'property';
    $value = 'test';

    $this->dataspace->expectOnce('set', array($property, $value));

    $this->object->set($property, $value);
  }

  function testGetByIndexString()
  {
    $value = 'test';

    $this->dataspace->setReturnValue('getByIndexString', $value, array($index = '[test]', null));

    $this->assertEqual($this->object->getByIndexString($index), $value);
  }

  function testSetByIndexString()
  {
    $path = '[path]';
    $value = 'test';

    $this->dataspace->expectOnce('setByIndexString', array($path, $value));

    $this->object->setByIndexString($path, $value);
  }

  function testRemove()
  {
    $this->dataspace->expectOnce('remove', array($property = 'test'));

    $this->object->remove($property);
  }

  function testReset()
  {
    $this->dataspace->expectOnce('reset');

    $this->object->reset();
  }
}

?>