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
require_once(LIMB_DIR . '/core/request_resolvers/ActionRequestResolver.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');

class ActionRequestResolverTest extends LimbTestCase
{
  function ActionRequestResolverTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function testGetRequestedActionOkActionFound()
  {
    $resolver = new ActionRequestResolver();

    $request = new Request();
    $request->set('action', $action = 'whatever');

    $this->assertEqual($resolver->resolve($request), $action);
  }

  function testGetRequestedActionNotSet()
  {
    $resolver = new ActionRequestResolver();

    $request = new Request();
    $this->assertFalse($resolver->resolve($request));
  }
}

?>
