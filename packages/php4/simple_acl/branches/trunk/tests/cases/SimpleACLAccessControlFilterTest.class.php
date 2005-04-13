<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ImageObjectsDAOTest.class.php 1093 2005-02-07 15:17:20Z pachanga $
*
***********************************************************************************/
require_once(LIMB_SIMPLE_ACL_DIR . '/filters/SimpleACLAccessControlFilter.class.php');
require_once(LIMB_DIR . '/core/services/Service.class.php');
require_once(LIMB_DIR . '/core/filters/FiltersChain.class.php');
require_once(LIMB_SIMPLE_ACL_DIR . '/SimpleACLAuthorizer.class.php');

Mock :: generate('FilterChain');
Mock :: generate('Service');
Mock :: generate('SimpleACLAuthorizer');

class SimpleACLAccessControlFilterTest extends LimbTestCase
{
  var $cmd;

  function SimpleACLAccessControlFilterTest()
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

  function testRunOkAccessGranted()
  {
    $toolkit =& Limb :: toolkit();

    $request =& $toolkit->getRequest();
    $response = $toolkit->getResponse();

    $service = new MockService($this);
    $service->expectOnce('getCurrentAction');
    $service->setReturnValue('getCurrentAction', $action = 'whatever');

    $toolkit->setCurrentService($service);

    $authorizer =& MockSimpleACLAuthorizer($this);
    $authorizer->expectOnce('canDo', array($action, $object));

    $fc = new MockFilterChain($this);
    $fc->expectOnce('next');

    $filter = new SimpleACLAccessControlFilter();
    $filter->run($fc, $request, $response);

    $service =& $toolkit->getCurrentService();

    $fc->tally();
    $resolver->tally();
    $service->tally();
  }

}

?>