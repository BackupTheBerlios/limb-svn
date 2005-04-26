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
require_once(LIMB_DIR . '/core/filters/UOWFilter.class.php');
require_once(LIMB_DIR . '/core/UnitOfWork.class.php');

Mock :: generate('FilterChain');
Mock :: generate('UnitOfWork');

Mock :: generatePartial('LimbBaseToolkit',
                        'ToolkitUOWFilterTestVersion',
                        array('getUOW'));

class UOWFilterTest extends LimbTestCase
{
  var $toolkit;

  function UOWFilterTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->uow = new MockUnitOfWork($this);

    $this->toolkit = new ToolkitUOWFilterTestVersion($this);
    $this->toolkit->setReturnReference('getUOW', $this->uow);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->uow->tally();
    Limb :: restoreToolkit();
  }

  function testRunOk()
  {
    $this->uow->expectOnce('start');
    $this->uow->expectOnce('commit');

    $filter = new UOWFilter();

    $fc = new MockFilterChain($this);
    $fc->expectOnce('next');

    $filter->run($fc, $request, $response, new DataSpace());

    $fc->tally();
  }

}

?>
