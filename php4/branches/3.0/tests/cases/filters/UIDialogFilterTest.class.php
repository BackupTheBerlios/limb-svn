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
require_once(LIMB_DIR . '/core/services/Service.class.php');
require_once(LIMB_DIR . '/core/filters/UIDialogFilter.class.php');

Mock :: generate('FilterChain');

class UIDialogFilterTest extends LimbTestCase
{
  var $toolkit;
  var $fc;

  function UIDialogFilterTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->toolkit =& Limb :: saveToolkit();
    $this->fc = new MockFilterChain($this);
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
    $this->fc->tally();
  }

  function testRunTransparent()
  {
    $request = $this->toolkit->getRequest();

    $filter = new UIDialogFilter();
    $filter->run($this->fc, $request, $response);
  }

  function testRunTransparentNon404Service()
  {
    $request = $this->toolkit->getRequest();
    $request->set('dialog', 1);

    $service = new Service('Any');
    $this->toolkit->setService($service);

    $filter = new UIDialogFilter();
    $filter->run($this->fc, $request, $response);

    $this->assertEqual($this->toolkit->getService(), $service);
  }

  function testRunNoNeedToReplaceService()
  {
    $request = $this->toolkit->getRequest();

    $service404 = new Service('404');
    $this->toolkit->setService($service404);

    $filter = new UIDialogFilter();
    $filter->run($this->fc, $request, $response);

    $this->assertEqual($this->toolkit->getService(), $service404);
  }

  function testRunServiceReplaced()
  {
    $request = $this->toolkit->getRequest();
    $request->set('from_dialog', 1);

    $service404 = new Service('404');
    $this->toolkit->setService($service404);

    $filter = new UIDialogFilter();
    $filter->run($this->fc, $request, $response);

    $this->assertEqual($this->toolkit->getService(),
                       new Service('UIHandleDialog'));
  }
}

?>
