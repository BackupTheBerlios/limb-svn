<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: DisplayViewCommandTest.class.php 1248 2005-04-19 15:07:09Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/PutRequestResolverResultToContextCommand.class.php');
require_once(LIMB_DIR . '/core/request_resolvers/RequestResolver.interface.php');

Mock :: generate('RequestResolver');

class PutRequestResolverResultToContextCommandTest extends LimbTestCase
{
  function PutRequestResolverResultToContextCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testPerform()
  {
    $toolkit =& Limb :: toolkit();

    $resolver = new MockRequestResolver($this);
    $toolkit->setRequestResolver($resolver_name = 'some_resovler', $resolver);

    $command = new PutRequestResolverResultToContextCommand($resolver_name,
                                                            $field_name = 'field_name');

    $request =& $toolkit->getRequest();
    $resolver->setReturnReference('resolve', $result, array($request));

    $context = new DataSpace();
    $this->assertEqual($context->getObject($field_name), $result);
  }
}

?>