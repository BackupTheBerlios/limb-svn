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
require_once(LIMB_DIR . '/class/core/object.class.php');
require_once(LIMB_DIR . '/class/core/dataspace.class.php');

Mock :: generate('dataspace');
Mock :: generatePartial('object',
                        'object_test_version',
                        array('_create_dataspace'));

class object_test extends LimbTestCase
{
  var $object;
  var $dataspace;

  function setUp()
  {
    $this->dataspace = new Mockdataspace($this);

    $this->object = new object_test_version($this);
    $this->object->setReturnValue('_create_dataspace', $this->dataspace);
    $this->object->__construct();
  }

  function tearDown()
  {
    $this->dataspace->tally();
  }

  function test_import()
  {
    $values = array('test');

    $this->dataspace->expectOnce('import', array($values));

    $this->object->import($values);
  }

  function test_merge()
  {
    $values = array('test');

    $this->dataspace->expectOnce('merge', array($values));

    $this->object->merge($values);
  }

  function test_export()
  {
    $values = array('test');

    $this->dataspace->setReturnValue('export', $values);

    $this->assertEqual($this->object->export(), $values);
  }

  function test_has_attribute_true1()
  {
    $values = array('test');

    $this->dataspace->setReturnValue('get', 1, array($property = 'test'));

    $this->assertTrue($this->object->has_attribute($property));
  }

  function test_has_attribute_true2()
  {
    $values = array('test');

    $this->dataspace->setReturnValue('get', 0, array($property = 'test'));

    $this->assertTrue($this->object->has_attribute($property));
  }

  function test_has_attribute_true3()
  {
    $values = array('test');

    $this->dataspace->setReturnValue('get', '', array($property = 'test'));

    $this->assertTrue($this->object->has_attribute($property));
  }

  function test_has_attribute_false()
  {
    $values = array('test');

    $this->dataspace->setReturnValue('get', null, array($property = 'test'));

    $this->assertFalse($this->object->has_attribute($property));
  }

  function test_get()
  {
    $value = 'test';

    $this->dataspace->setReturnValue('get', $value, array($property = 'test', null));

    $this->assertEqual($this->object->get($property), $value);
  }

  function test_set()
  {
    $property = 'property';
    $value = 'test';

    $this->dataspace->expectOnce('set', array($property, $value));

    $this->object->set($property, $value);
  }

  function test_get_by_index_string()
  {
    $value = 'test';

    $this->dataspace->setReturnValue('get_by_index_string', $value, array($index = '[test]', null));

    $this->assertEqual($this->object->get_by_index_string($index), $value);
  }

  function test_set_by_index_string()
  {
    $path = '[path]';
    $value = 'test';

    $this->dataspace->expectOnce('set_by_index_string', array($path, $value));

    $this->object->set_by_index_string($path, $value);
  }

  function test_destroy()
  {
    $this->dataspace->expectOnce('destroy', array($property = 'test'));

    $this->object->destroy($property);
  }

  function test_reset()
  {
    $this->dataspace->expectOnce('reset');

    $this->object->reset();
  }
}

?>