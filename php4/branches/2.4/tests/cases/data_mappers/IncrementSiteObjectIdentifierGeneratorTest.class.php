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
require_once(LIMB_DIR . '/class/core/site_objects/SiteObject.class.php');
require_once(LIMB_DIR . '/class/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/class/core/data_mappers/IncrementSiteObjectIdentifierGenerator.class.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');

Mock :: generate('LimbToolkit');
Mock :: generate('SiteObject');
Mock :: generate('Tree');

class IncrementSiteObjectIdentifierGeneratorTest extends LimbTestCase
{
  var $object;
  var $generator;
  var $tree;
  var $toolkit;

  function setUp()
  {
    $this->object = new MockSiteObject($this);
    $this->generator = new IncrementSiteObjectIdentifierGenerator();
    $this->tree = new MockTree($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnReference('getTree', $this->tree);

    $this->object->expectOnce('getParentNodeId');
    $this->object->setReturnValue('getParentNodeId', 100);

    $this->tree->expectOnce('getMaxChildIdentifier', array(100));

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->object->tally();
    $this->tree->tally();
    $this->toolkit->tally();

    Limb :: popToolkit();
  }

  function testGenerateFalse()
  {
    $this->tree->setReturnValue('getMaxChildIdentifier', false);
    $this->assertIdentical($this->generator->generate($this->object), false);
  }

  function testGenerateForNumber()
  {
    $this->tree->setReturnValue('getMaxChildIdentifier', 0);
    $this->assertEqual($this->generator->generate($this->object), 1);
  }

  function testGenerateForNumber2()
  {
    $this->tree->setReturnValue('getMaxChildIdentifier', 1000);
    $this->assertEqual($this->generator->generate($this->object), 1001);
  }

  function testGenerateForText()
  {
    $this->tree->setReturnValue('getMaxChildIdentifier', 'ru');
    $this->assertEqual($this->generator->generate($this->object), 'ru1');
  }

  function testGenerateForText2()
  {
    $this->tree->setReturnValue('getMaxChildIdentifier', '119');
    $this->assertEqual($this->generator->generate($this->object), '120');
  }

  function testGenerateForTextEndingWithNumber()
  {
    $this->tree->setReturnValue('getMaxChildIdentifier', 'test10');
    $this->assertEqual($this->generator->generate($this->object), 'test11');
  }

  function testGenerateForTextEndingWithNumber2()
  {
    $this->tree->setReturnValue('getMaxChildIdentifier', '4test19');
    $this->assertEqual($this->generator->generate($this->object), '4test20');
  }

  function testGenerateForTextEndingWithNumber3()
  {
    $this->tree->setReturnValue('getMaxChildIdentifier', '4te10st19');
    $this->assertEqual($this->generator->generate($this->object), '4te10st20');
  }
}

?>