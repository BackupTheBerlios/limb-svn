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
require_once(dirname(__FILE__) . '/../../../site_objects/ImageObject.class.php');
require_once(dirname(__FILE__) . '/../../../ImageVariation.class.php');
require_once(dirname(__FILE__) . '/../../../MediaManager.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');

Mock :: generate('ImageVariation');

class ImageObjectTest extends LimbTestCase
{
  var $variation;
  var $image_object;

  function setUp()
  {
    $this->variation = new MockImageVariation($this);
    $this->image_object = new ImageObject();
  }

  function tearDown()
  {
    $this->variation->tally();
  }

  function testGetVariationsEmpty()
  {
    $this->assertTrue(!$this->image_object->getVariations());
  }

  function testGetVariationFailed()
  {
    $this->assertNull($this->image_object->getVariation('original'));
  }

  function testAttachVariation()
  {
    $this->variation->expectOnce('getName');
    $this->variation->setReturnValue('getName', $name = 'original');

    $this->image_object->attachVariation($this->variation);

    $this->assertTrue($this->image_object->getVariation($name) === $this->variation);

    $this->assertTrue($this->image_object->getVariations() === array($name => $this->variation));
  }
}

?>