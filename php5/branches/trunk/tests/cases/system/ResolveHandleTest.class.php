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
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');

class UnaffectedObject
{
  var $test_var = 'default';
}

class DeclaredInSameFile
{
    var $test_var = 'default';

    function declaredInSameFile($var = 'construction_default')
    {
        $this->test_var = $var;
    }
}

class ResolveHandleTest extends LimbTestCase
{
  function testNullHandle()
  {
    $handle = null;
    resolveHandle($handle);
    $this->assertNull($handle);
  }

  function testObjectUnaffected()
  {
    $handle = new UnaffectedObject();
    $obj =& $handle;
    $obj->test_var = 'changed';
    resolveHandle($handle);
    $this->assertIsA($handle, 'UnaffectedObject');
    $this->assertIdentical($handle, $obj);
    $this->assertEqual($handle->test_var, 'changed');
  }

  function testClassDeclaredInSameFile()
  {
    $handle = 'DeclaredInSameFile';
    resolveHandle($handle);
    $this->assertIsA($handle, 'DeclaredInSameFile');
  }

  function testLoadClassFile1()
  {
    $this->assertFalse(class_exists('LoadedHandleClass'));
    $handle = dirname(__FILE__) . '/handle.inc.php|LoadedHandleClass';
    resolveHandle($handle);
    $this->assertIsA($handle, 'LoadedHandleClass');
    $this->assertTrue(class_exists('LoadedHandleClass'));
  }

  function testLoadClassFile2()
  {
    $this->assertFalse(class_exists('TestHandleClass'));
    $handle = dirname(__FILE__) . '/TestHandleClass';
    resolveHandle($handle);
    $this->assertIsA($handle, 'TestHandleClass');
    $this->assertTrue(class_exists('TestHandleClass'));
  }

  function testLoadClassFileException()
  {
    $handle = array(dirname(__FILE__) . '/TestHandleClass', 1, 2, 3, 4, 5);

    try
    {
      resolveHandle($handle);
      $this->assertTrue(false);
    }
    catch(Exception $e)
    {
      $this->assertEqual($e->getMessage(), 'too many arguments for resolve handle');
    }
  }

  function testConstructor()
  {
    $handle = array('DeclaredInSameFile', 'construction_parameter');
    resolveHandle($handle);
    $this->assertIsA($handle, 'DeclaredInSameFile');
    $this->assertEqual($handle->test_var, 'construction_parameter');
  }


}
?>