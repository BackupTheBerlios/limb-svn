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
require_once(LIMB_DIR . '/class/core/DomainObject.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');

class DomainObjectTest extends LimbTestCase
{
  var $object;

  function setUp()
  {
    $this->object = new DomainObject();
  }

  function tearDown()
  {
  }

  function testGetId()
  {
    $this->object->setId(10);
    $this->assertEqual($this->object->getId(), 10);
  }

  function testIsDirtyFalse()
  {
    $this->assertFalse($this->object->isDirty());
  }

  function testObjectBecomesCleanAfterImport()
  {
    $this->assertFalse($this->object->isDirty());

    $this->object->set('test', 'value');

    $this->assertTrue($this->object->isDirty());

    $values = array('test');

    $this->object->import($values);

    $this->assertFalse($this->object->isDirty());
  }

  function testObjectBecomesDirtyAfterSet()
  {
    $this->assertFalse($this->object->isDirty());

    $this->object->set('test', 'value');

    $this->assertTrue($this->object->isDirty());
  }

  function testMarkClean()
  {
    $this->object->set('test', 'value');
    $this->object->markClean();

    $this->assertFalse($this->object->isDirty());
  }

  function testObjectBecomesDirtyAfterGetNonexistingReference()
  {
    $property =& $this->object->getReference('test');

    $this->assertTrue($this->object->isDirty());
  }

  function testObjectBecomesDirtyAfterReferenceGotChanged1()
  {
    $this->object->import(array('test' => new Object()));

    $obj = $this->object->get('test');

    $this->assertFalse($this->object->isDirty());

    $obj->set('whatever', 1);

    $this->assertTrue($this->object->isDirty());
  }

  function testObjectBecomesDirtyAfterReferenceGotChanged2()
  {
    $this->object->import(array('test' => 2));

    $ref =& $this->object->getReference('test');

    $this->assertFalse($this->object->isDirty());

    $ref = 1;

    $this->assertTrue($this->object->isDirty());
  }

}

?>