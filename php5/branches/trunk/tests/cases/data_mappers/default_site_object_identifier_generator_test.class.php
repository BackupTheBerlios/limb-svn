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
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/class/core/data_mappers/default_site_object_identifier_generator.class.php');

Mock :: generate('site_object');

class default_site_object_identifier_generator_test extends LimbTestCase
{
  var $object;
  var $generator;

  function setUp()
  {
    $this->object = new Mocksite_object($this);
    $this->generator = new DefaultSiteObjectIdentifierGenerator();
  }

  function tearDown()
  {
    $this->object->tally();
  }

  function test_generate()
  {
    $this->object->expectOnce('get_identifier');
    $this->object->setReturnValue('get_identifier', 'test');
    $this->assertEqual('test', $this->generator->generate($this->object));
  }
}

?>