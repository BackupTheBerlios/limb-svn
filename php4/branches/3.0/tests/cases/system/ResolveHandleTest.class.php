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
require_once(LIMB_DIR . '/core/system/objects_support.inc.php');

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
  function ResolveHandleTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function testClassDeclaredInSameFile()
  {
    $handle = new LimbHandle('DeclaredInSameFile');
    $this->assertIsA(Handle :: resolve($handle), 'DeclaredInSameFile');
  }

  function testLoadClassFile1()
  {
    $this->assertFalse(class_exists('LoadedHandleClass'));
    $handle = new LimbHandle(dirname(__FILE__) . '/handle.inc.php|LoadedHandleClass');
    $this->assertIsA(Handle :: resolve($handle), 'LoadedHandleClass');
    $this->assertTrue(class_exists('LoadedHandleClass'));
  }

  function testLoadClassFile2()
  {
    $this->assertFalse(class_exists('TestHandleClass'));
    $handle = new LimbHandle(dirname(__FILE__) . '/TestHandleClass');
    $this->assertIsA(Handle :: resolve($handle), 'TestHandleClass');
    $this->assertTrue(class_exists('TestHandleClass'));
  }

}
?>