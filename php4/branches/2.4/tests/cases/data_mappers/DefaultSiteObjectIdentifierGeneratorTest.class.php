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
require_once(LIMB_DIR . '/class/core/data_mappers/DefaultSiteObjectIdentifierGenerator.class.php');

Mock :: generate('SiteObject');

class DefaultSiteObjectIdentifierGeneratorTest extends LimbTestCase
{
  var $object;
  var $generator;

  function DefaultSiteObjectIdentifierGeneratorTest()
  {
    parent :: LimbTestCase('default identifier generator test');
  }

  function setUp()
  {
    $this->object = new MockSiteObject($this);
    $this->generator = new DefaultSiteObjectIdentifierGenerator();
  }

  function tearDown()
  {
    $this->object->tally();
  }

  function testGenerate()
  {
    $this->object->expectOnce('getIdentifier');
    $this->object->setReturnValue('getIdentifier', 'test');
    $this->assertEqual('test', $this->generator->generate($this->object));
  }
}

?>