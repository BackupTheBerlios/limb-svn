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
require_once(LIMB_DIR . '/class/template/components/LimbPagerComponent.class.php');
require_once(LIMB_DIR . '/class/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/class/request/Request.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('Request');

class LimbPagerComponentTest extends LimbTestCase
{
  var $component;
  var $request;

  function LimbPagerComponentTest()
  {
    parent :: LimbTestCase('limb pager component test');

    $toolkit =& Limb :: toolkit();
    $this->request =& $toolkit->getRequest();
  }

  function setUp()
  {
    $this->component = new LimbPagerComponent();
  }

  function tearDown()
  {
  }

  function testPrepare()
  {
    $this->component->id = $id = 'navigator';

    $this->request->set($this->component->getPagerId(), 2);

    $this->component->setTotalItems(100);
    $this->component->setItemsPerPage(10);
    $this->component->setPagesPerSection(5);

    $this->component->prepare();

    $this->assertEqual($this->component->getCurrentPage(), 2);
    $this->assertFalse($this->component->isCurrentPage());
    $this->assertEqual($this->component->getPageCounter(), 0);
    $this->assertEqual($this->component->getPagesCount(), 10);
    $this->assertTrue($this->component->hasMoreThanOnePage());
    $this->assertEqual($this->component->getSectionBeginPageNumber(), 1);
    $this->assertEqual($this->component->getSectionEndPageNumber(), 5);
    $this->assertTrue($this->component->hasNext());
    $this->assertTrue($this->component->hasPrev());
    $this->assertFalse($this->component->hasSectionChanged());
    $this->assertEqual($this->component->getCurrentPageBeginItemNumber(), 11);
    $this->assertEqual($this->component->getCurrentPageEndItemNumber(), 20);
  }

  function testPrepareNoItemsSet()
  {
    $this->component->id = $id = 'navigator';

    $this->request->set($this->component->getPagerId(), 2);

    $this->component->setTotalItems(0);
    $this->component->setItemsPerPage(10);
    $this->component->setPagesPerSection(5);

    $this->component->prepare();

    $this->assertEqual($this->component->getCurrentPage(), 1);
    $this->assertEqual($this->component->getPageCounter(), 0);
    $this->assertFalse($this->component->isCurrentPage());
    $this->assertEqual($this->component->getPagesCount(), 1);
    $this->assertFalse($this->component->hasMoreThanOnePage());
    $this->assertEqual($this->component->getSectionBeginPageNumber(), 1);
    $this->assertEqual($this->component->getSectionEndPageNumber(), 1);
    $this->assertFalse($this->component->hasNext());
    $this->assertFalse($this->component->hasPrev());
    $this->assertFalse($this->component->hasSectionChanged());
    $this->assertEqual($this->component->getCurrentPageBeginItemNumber(), 0);
    $this->assertEqual($this->component->getCurrentPageEndItemNumber(), 0);
  }

  function testNext()
  {
    $this->component->id = $id = 'navigator';

    $this->request->set($this->component->getPagerId(), 2);

    $this->component->setTotalItems(40);
    $this->component->setItemsPerPage(10);
    $this->component->setPagesPerSection(5);

    $this->component->prepare();

    $this->assertEqual($this->component->getPageCounter(), 0);//???

    $this->assertTrue($this->component->next());

    $this->assertEqual($this->component->getPageCounter(), 1);

    $this->assertFalse($this->component->hasSectionChanged());
    $this->assertEqual($this->component->getSectionCounter(), 1);
  }

  function testNextSectionhasChanged()
  {
    $this->component->id = $id = 'navigator';

    $this->request->set($this->component->getPagerId(), 2);

    $this->component->setTotalItems(40);
    $this->component->setItemsPerPage(10);
    $this->component->setPagesPerSection(3);

    $this->component->prepare();

    $this->assertEqual($this->component->getPageCounter(), 0);
    $this->component->next();
    $this->component->next();

    $this->assertTrue($this->component->isCurrentPage());

    $this->component->next();
    $this->assertFalse($this->component->hasSectionChanged());

    $this->assertTrue($this->component->next());
    $this->assertTrue($this->component->hasSectionChanged());

    $this->assertFalse($this->component->next());
  }

  function testGetFirstPageUri()
  {
    $uri = new Uri('http://test.com?p1=wow&p2[3]=yo');

    $toolkit =& new MockLimbToolkit($this);
    $request =& new MockRequest($this);

    $request->setReturnValue('getUri', $uri);
    $request->setReturnValue('export', array('p1' => ' wow ', 'p2' => array('3' => 'yo')));

    $toolkit->setReturnReference('getRequest', $request);
    Limb :: registerToolkit($toolkit);

    $this->component->setPagerPrefix('p');
    $this->component->id = 'navi';
    $this->component->prepare();

    $uri = $this->component->getPageUri(1);

    $this->assertEqual($uri, 'http://test.com?p1=+wow+&p2[3]=yo');

    Limb :: popToolkit();
  }

  function testGetFirstPageUriNoQuery()
  {
    $uri = new Uri('http://test.com');

    $toolkit =& new MockLimbToolkit($this);
    $request =& new MockRequest($this);

    $request->setReturnValue('getUri', $uri);
    $request->setReturnValue('export', array());

    $toolkit->setReturnReference('getRequest', $request);
    Limb :: registerToolkit($toolkit);

    $this->component->setPagerPrefix('p');
    $this->component->id = 'navi';
    $this->component->prepare();

    $uri = $this->component->getPageUri(1);

    $this->assertEqual($uri, 'http://test.com');

    Limb :: popToolkit();
  }

  function testGetPageUri()
  {
    $uri = new Uri('http://test.com?p1=wow&p2[3]=yo');

    $toolkit =& new MockLimbToolkit($this);
    $request =& new MockRequest($this);

    $request->setReturnValue('getUri', $uri);
    $request->setReturnValue('export', array('p1' => 'wow', 'p2' => array('3' => ' yo ')));

    $toolkit->setReturnReference('getRequest', $request);
    Limb :: registerToolkit($toolkit);

    $this->component->setPagerPrefix('p');
    $this->component->id = 'navi';
    $this->component->prepare();

    $uri = $this->component->getPageUri(2);

    $this->assertEqual($uri, 'http://test.com?p1=wow&p2[3]=+yo+&p_navi=2');

    Limb :: popToolkit();
  }
}

?>