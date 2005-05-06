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
require_once(LIMB_DIR . '/core/filters/FilterChain.class.php');
require_once(LIMB_SIMPLE_ACL_DIR . '/filters/SimpleACLAccessFilter.class.php');
require_once(LIMB_SIMPLE_ACL_DIR . 'SimpleACLBaseToolkit.class.php');
require_once(LIMB_SIMPLE_ACL_DIR . 'SimpleACLAuthorizer.class.php');

Mock :: generate('SimpleACLBaseToolkit');
Mock :: generate('SimpleACLAuthorizer');
Mock :: generate('FilterChain');

class SimpleACLAccessFilterTest extends LimbTestCase
{
  function SimpleACLAccessFilterTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->acl_toolkit = new MockSimpleACLBaseToolkit($this);
    $this->authorizer = new MockSimpleACLAuthorizer($this);

    $this->acl_toolkit->setReturnReference('getAuthorizer', $this->authorizer);

    Limb :: registerToolkit($this->acl_toolkit, 'SimpleACL');

    Limb :: saveToolkit();
  }

  function tearDown()
  {
    $this->acl_toolkit->tally();
    $this->authorizer->tally();

    Limb :: restoreToolkit();
    Limb :: restoreToolkit('SimpleACL');

    ClearTestingIni();
  }

  function testRunOkIfServiceNotFound()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    $filter = new SimpleACLAccessFilter();

    $fc = new MockFilterChain($this);
    $fc->expectOnce('next');

    $filter->run($fc, $request, $response, new DataSpace());

    $fc->tally();
  }

  function testRunOk()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath($path = 'whatever');

    $service = new Service($service_name = 'TestService');
    $service->setCurrentAction($action = 'some_action');

    $this->authorizer->expectOnce('canDo', array($action, $path, $service_name));
    $this->authorizer->setReturnValue('canDo', true);

    $filter = new SimpleACLAccessFilter();

    $fc = new MockFilterChain($this);
    $fc->expectOnce('next');

    $context = new DataSpace();
    $context->setObject('Service', $service);

    $filter->run($fc, $request, $response, $context);

    $fc->tally();
  }

  function testRunAccessDenied()
  {
    RegisterTestingIni('403.service.ini',
                       '
                       default_action = display
                       [display]
                       props');

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath($path = 'whatever');

    $service = new Service($service_name = 'TestService');
    $service->setCurrentAction($action = 'some_action');

    $this->authorizer->expectOnce('canDo', array($action, $path, $service_name));
    $this->authorizer->setReturnValue('canDo', false);

    $filter = new SimpleACLAccessFilter();

    $fc = new MockFilterChain($this);
    $fc->expectOnce('next');

    $context = new DataSpace();
    $context->setObject('Service', $service);

    $filter->run($fc, $request, $response, $context);

    $new_service =& $context->getObject('Service');
    $this->assertEqual($new_service->getName(), '403');
    $this->assertEqual($new_service->getCurrentAction(), 'display');

    $fc->tally();
  }
}

?>
