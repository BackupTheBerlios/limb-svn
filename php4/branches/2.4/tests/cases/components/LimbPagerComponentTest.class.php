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

  function testReset()
  {
    $this->component->id = $id = 'navigator';

    $this->request->set($this->component->getPagerId(), 2);

    $this->component->setItemsPerPage(10);
    $this->component->setPagesPerSection(5);
    $this->component->setTotalItems(100);

    $this->component->reset();

    $this->assertEqual($this->component->getDisplayedPage(), 2);
    $this->assertFalse($this->component->isDisplayedPage());
    $this->assertEqual($this->component->getPage(), 1);
    $this->assertEqual($this->component->getTotalPages(), 10);
    $this->assertEqual($this->component->getPagesPerSection(), 5);
    $this->assertTrue($this->component->hasMoreThanOnePage());
    $this->assertEqual($this->component->getSectionBeginPage(), 1);
    $this->assertEqual($this->component->getSectionEndPage(), 5);
    $this->assertTrue($this->component->hasNext());
    $this->assertTrue($this->component->hasPrev());
    $this->assertEqual($this->component->getDisplayedPageBeginItem(), 11);
    $this->assertEqual($this->component->getDisplayedPageEndItem(), 20);
  }

  function testResetTotalItemsZero()
  {
    $this->component->id = $id = 'navigator';

    $this->request->set($this->component->getPagerId(), 2);

    $this->component->setItemsPerPage(10);
    $this->component->setPagesPerSection(5);
    $this->component->setTotalItems(0);

    $this->component->reset();

    $this->assertEqual($this->component->getDisplayedPage(), 1);
    $this->assertEqual($this->component->getPage(), 1);
    $this->assertTrue($this->component->isDisplayedPage());
    $this->assertEqual($this->component->getTotalPages(), 1);
    $this->assertFalse($this->component->hasMoreThanOnePage());
    $this->assertEqual($this->component->getSectionBeginPage(), 1);
    $this->assertEqual($this->component->getSectionEndPage(), 1);
    $this->assertFalse($this->component->hasNext());
    $this->assertFalse($this->component->hasPrev());
    $this->assertEqual($this->component->getDisplayedPageBeginItem(), 0);
    $this->assertEqual($this->component->getDisplayedPageEndItem(), 0);
  }

  function testNextPage()
  {
    $this->component->id = $id = 'navigator';

    $this->request->set($this->component->getPagerId(), 2);

    $this->component->setTotalItems(40);
    $this->component->setItemsPerPage(10);
    $this->component->setPagesPerSection(5);

    $this->component->reset();

    $this->assertEqual($this->component->getPage(), 1);

    $this->assertTrue($this->component->nextPage());
    $this->assertTrue($this->component->isValid());

    $this->assertEqual($this->component->getPage(), 2);
  }

  function testNextPageOutOfBounds()
  {
    $this->component->id = $id = 'navigator';

    $this->request->set($this->component->getPagerId(), 2);

    $this->component->setTotalItems(40);
    $this->component->setItemsPerPage(10);

    $this->component->reset();

    $this->assertTrue($this->component->nextPage());
    $this->assertTrue($this->component->isValid());

    $this->assertTrue($this->component->nextPage());
    $this->assertTrue($this->component->isValid());

    $this->assertTrue($this->component->nextPage());
    $this->assertTrue($this->component->isValid());

    $this->assertFalse($this->component->nextPage());
    $this->assertFalse($this->component->isValid());
  }

  function testSectionNumbers()
  {
    $this->component->id = $id = 'navigator';

    $this->request->set($this->component->getPagerId(), 2);

    $this->component->setTotalItems(40);
    $this->component->setItemsPerPage(3);
    $this->component->setPagesPerSection(10);

    $this->component->reset();

    $this->component->nextPage();

    $this->assertEqual($this->component->getSection(), 1);
    $this->assertEqual($this->component->getSectionBeginPage(), 1);
    $this->assertEqual($this->component->getSectionEndPage(), 10);
  }

  function testSectionNumbersRightBound()
  {
    $this->component->id = $id = 'navigator';

    $this->request->set($this->component->getPagerId(), 2);

    $this->component->setTotalItems(40);
    $this->component->setItemsPerPage(10);// 4 pages total
    $this->component->setPagesPerSection(10);

    $this->component->reset();

    $this->component->nextPage();

    $this->assertEqual($this->component->getSection(), 1);
    $this->assertEqual($this->component->getSectionBeginPage(), 1);
    $this->assertEqual($this->component->getSectionEndPage(), 4);
  }

  function testNextSection()
  {
    $this->component->id = $id = 'navigator';

    $this->request->set($this->component->getPagerId(), 2);

    $this->component->setTotalItems(40);
    $this->component->setItemsPerPage(5);
    $this->component->setPagesPerSection(2);

    $this->component->reset();

    $this->assertTrue($this->component->nextSection());
    $this->assertTrue($this->component->nextSection());
    $this->assertTrue($this->component->nextSection());
    $this->assertFalse($this->component->nextSection());
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
    $this->component->reset();

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
    $this->component->reset();

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
    $this->component->reset();

    $uri = $this->component->getPageUri(2);

    $this->assertEqual($uri, 'http://test.com?p1=wow&p2[3]=+yo+&p_navi=2');

    Limb :: popToolkit();
  }

  function testGetPrevSectionUri()
  {
    $uri = new Uri('http://test.com');

    $toolkit =& new MockLimbToolkit($this);
    $request =& new MockRequest($this);

    $request->setReturnValue('getUri', $uri);

    $toolkit->setReturnReference('getRequest', $request);
    Limb :: registerToolkit($toolkit);

    $this->component->setPagerPrefix('p');
    $this->component->id = 'nav';
    $this->component->setTotalItems(60);
    $this->component->setItemsPerPage(10);
    $this->component->setPagesPerSection(2);

    $request->setReturnValue('export', array('p_nav' => 3));

    $this->component->reset();

    $this->component->nextPage();

    $uri = $this->component->getSectionUri();

    $this->assertEqual($uri, 'http://test.com?p_nav=2');
    $this->assertEqual($this->component->getSectionBeginPage(), 1);
    $this->assertEqual($this->component->getSectionEndPage(), 2);

    Limb :: popToolkit();
  }

  function testGetNextSectionUri()
  {
    $uri = new Uri('http://test.com');

    $toolkit =& new MockLimbToolkit($this);
    $request =& new MockRequest($this);

    $request->setReturnValue('getUri', $uri);

    $toolkit->setReturnReference('getRequest', $request);
    Limb :: registerToolkit($toolkit);

    $this->component->setPagerPrefix('p');
    $this->component->id = 'nav';
    $this->component->setTotalItems(60);
    $this->component->setItemsPerPage(10);
    $this->component->setPagesPerSection(2);

    $request->setReturnValue('export', array('p_nav' => 3));

    $this->component->reset();

    for($i = 0; $i < 5; $i++)
      $this->component->nextPage();

    $uri = $this->component->getSectionUri(2);

    $this->assertEqual($uri, 'http://test.com?p_nav=5');
    $this->assertEqual($this->component->getSectionBeginPage(), 5);
    $this->assertEqual($this->component->getSectionEndPage(), 6);

    Limb :: popToolkit();
  }
}

?>