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
require_once(LIMB_DIR . '/class/core/Dataspace.class.php');

SimpleTestOptions :: ignore('DataspaceTest');

class DataspaceTest extends LimbTestCase
{
  var $dataspace;
  var $filter;

  function setUp()
  {
    $this->dataspace = new Dataspace();
  }

  function tearDown()
  {
    unset($this->dataspace);
  }

  function testGetUnsetVariable()
  {
    $this->assertNull($this->dataspace->get('foo'));
  }

  function testGetSetVariable()
  {
    $this->dataspace->set('foo', 'bar');
    $this->assertIdentical($this->dataspace->get('foo'), 'bar');
  }

  function testGetSetArray()
  {
    $array = array('red', 'blue', 'green');
    $this->dataspace->set('foo', $array);
    $this->assertIdentical($this->dataspace->get('foo'), $array);
  }

  function testGetSetObject()
  {
    $foo = array('red', 'blue', 'green');
    $this->dataspace->set('foo', $foo);
    $this->assertIdentical($this->dataspace->get('foo'), $foo);
  }

  function testGetSetAppend()
  {
    $first = 'Hello';
    $second = 'World!';
    $this->dataspace->set('foo', $first);
    $this->dataspace->append('foo', $second);
    $this->assertIdentical($this->dataspace->get('foo'), $first . $second);
  }

  function testGetReference()
  {
    $foo =& $this->dataspace->getReference('foo');
    $foo = 'whatever';
    $this->assertEqual($this->dataspace->get('foo'), $foo);
  }

  function testGetReferenceDefaultValue()
  {
    $foo =& $this->dataspace->getReference('foo', array());
    $this->assertIdentical($foo, array());
    $foo['test1'] = 'whatever';
    $this->assertEqual($this->dataspace->get('foo'), $foo);
  }

  function testGetSetAppendMixedType()
  {
    $first = 'Hello';
    $second = 2;
    $this->dataspace->set('foo', $first);
    $this->dataspace->append('foo', $second);
    $this->assertIdentical($this->dataspace->get('foo'), $first . $second);
  }

  function testExportEmpty()
  {
    $foo = array();
    $this->assertIdentical($this->dataspace->export(), $foo);
  }

  function testExport()
  {
    $this->dataspace->set('foo', 'bar');
    $expected = array('foo' => 'bar');
    $this->assertIdentical($this->dataspace->export(), $expected);
  }

  function testExportImport()
  {
    $numbers = array(1, 2, 3);
    $foo = array('size' => 'big', 'color' => 'red', 'numbers' => $numbers);
    $this->dataspace->import($foo);
    $exported = $this->dataspace->export();
    $this->assertIdentical($exported['size'], 'big');
    $this->assertIdentical($exported['color'], 'red');
    $this->assertIdentical($exported['numbers'], $numbers);
  }

  function testExportImportAppend()
  {
    $numbers = array(1, 2, 3);
    $foo = array('numbers' => $numbers);
    $bar = array('size' => 'big', 'color' => 'red');
    $this->dataspace->import($foo);
    $this->dataspace->importAppend($bar);
    $exported = $this->dataspace->export();
    $this->assertIdentical($exported['size'], 'big');
    $this->assertIdentical($exported['color'], 'red');
    $this->assertIdentical($exported['numbers'], $numbers);
  }
  function testDuplicateImportAppend()
  {
    // experimental test case.  Should this be the proper behavior of importAppend
    // instead of what it does now?
    // I think so, why would you want to keep the original value rather than
    // using the new one? (Jon)
    $foo = array('foo' => 'kung');
    $this->dataspace->set('foo', 'bar');
    $this->dataspace->importAppend($foo);
    $expected = $this->dataspace->export();
    $this->assertIdentical($expected['foo'], 'kung');
  }

  function testUnset()
  {
    $array = array('rainbow' => array('color' => 'red'));
    $this->dataspace->import($array);

    $this->dataspace->destroy('rainbow');

    $this->assertNull($this->dataspace->get('rainbow'));
  }

  function testMerge()
  {
    $this->dataspace->import('');

    $this->dataspace->merge('');

    $this->assertTrue(is_array($all = $this->dataspace->export()));

    $this->dataspace->import(array('people' => array('Vasa')));

    $this->dataspace->merge(array('people' => array('Vasa', 'Bob')));

    $all = $this->dataspace->export();

    $this->assertEqual(count($all['people']), 2);

    $this->assertTrue((in_array('Bob', $all['people']) &&  in_array('Vasa', $all['people'])));
  }

  function testGetByIndexString()
  {
    $array = array('rainbow' => array('color' => 'red'));
    $this->dataspace->import($array);

    $this->assertTrue(Limb :: isError($this->dataspace->getByIndexString('""hkljkscc')));

    $this->assertTrue(Limb :: isError($this->dataspace->getByIndexString('["rainbow][color]')));

    $this->assertTrue(Limb :: isError($this->dataspace->getByIndexString('[rainbow["color"]]')));

    $this->assertNull($this->dataspace->getByIndexString('[rainbow][sound]'), 'undefined index');

    $this->assertEqual($this->dataspace->getByIndexString('[rainbow][color]'), 'red');
    $this->assertEqual($this->dataspace->getByIndexString('[rainbow]["color"]'), 'red');
    $this->assertEqual($this->dataspace->getByIndexString('["rainbow"][\'color\']'), 'red');
  }

  function testSetByIndexString()
  {
    $size_before = $this->dataspace->getSize();

    $this->dataspace->setByIndexString('""hkljkscc', 'test');
    $this->assertEqual($this->dataspace->getSize(), $size_before, 'invalid index string, nothing should be written');

    $this->dataspace->setByIndexString('["rainbow][color]', 'test');
    $this->assertEqual($this->dataspace->getSize(), $size_before, 'wrong quotation nesting, nothing should be written');

    $this->dataspace->setByIndexString('[rainbow["color"]]', 'test');
    $this->assertEqual($this->dataspace->getSize(), $size_before, 'wrong brackets nesting, nothing should be written');

    $this->dataspace->setByIndexString('[rainbow][color]', array(1 => 'red'));
    $this->assertEqual($this->dataspace->vars['rainbow']['color'], array(1 => 'red'));

    $this->dataspace->setByIndexString('[rainbow]["color"]', '"red"');
    $this->assertEqual($this->dataspace->vars['rainbow']['color'], '"red"');

    $this->dataspace->setByIndexString('["rainbow"][\'color\']', 10);
    $this->assertEqual($this->dataspace->vars['rainbow']['color'], 10);
  }
}
?>