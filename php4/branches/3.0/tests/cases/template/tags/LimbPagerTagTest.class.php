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
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/http/Uri.class.php');

Mock :: generate('LimbBaseToolkit', 'MockLimbToolkit');
Mock :: generate('Request');

class LimbPagerNavigatorTagTestCase extends LimbTestCase
{
  function LimbPagerNavigatorTagTestCase()
  {
    parent :: LimbTestCase('limb pager tag case');
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testSetParameters()
  {
    $template = '<limb:pager:NAVIGATOR id="test" items="25" pages_per_section="4" pager_prefix="nav">'.
                '</limb:pager:NAVIGATOR>';

    RegisterTestingTemplate('/limb/pager_navigator_default.html', $template);

    $page =& new Template('/limb/pager_navigator_default.html');

    $pager =& $page->getChild('test');

    $this->assertEqual($pager->getItemsPerPage(), 25);
    $this->assertEqual($pager->getPagesPerSection(), 4);
    $this->assertEqual($pager->getPagerId(), 'nav_test');
  }

  function testPager()
  {
    $uri = new Uri('test.com');

    $toolkit =& new MockLimbToolkit($this);
    $request =& new MockRequest($this);

    $toolkit->setReturnReference('getRequest', $request);
    Limb :: registerToolkit($toolkit);

    $request->setReturnValue('getUri', $uri);

    $template = '<limb:pager:NAVIGATOR id="test" items="10">'.
                '<limb:pager:FIRST>F-{$href}|</limb:pager:FIRST>' .
                '<limb:pager:PREV>P-{$href}|</limb:pager:PREV>' .
                '<limb:pager:LIST>'.
                '<limb:pager:CURRENT>C-{$href}|{$number}|</limb:pager:CURRENT>' .
                '<limb:pager:NUMBER>N-{$href}|{$number}|</limb:pager:NUMBER>' .
                '<limb:pager:SEPARATOR>**</limb:pager:SEPARATOR>' .
                '</limb:pager:LIST>'.
                '<limb:pager:NEXT>X-{$href}|</limb:pager:NEXT>' .
                '<limb:pager:LAST>L-{$href}|</limb:pager:LAST>' .
                '</limb:pager:NAVIGATOR>';

    RegisterTestingTemplate('/limb/pager_simple.html', $template);

    $page =& new Template('/limb/pager_simple.html');

    $navigator =& $page->findChild('test');

    $request->setReturnValue('export', array($navigator->getPagerId() => 2));
    $request->setReturnValue('get', 2, array($navigator->getPagerId()));

    $navigator->setTotalItems(40);
    $navigator->prepare();

    $expected = 'F-test.com|' .
                'P-test.com|'.
                'N-test.com|1|**'.
                'C-test.com?page_test=2|2|**'.
                'N-test.com?page_test=3|3|**N-test.com?page_test=4|4|'.
                'X-test.com?page_test=3|'.
                'L-test.com?page_test=4|';

    $this->assertEqual($page->capture(), $expected);

    Limb :: restoreToolkit();
  }

  function testPagerProperties()
  {
    $uri = new Uri('test.com');

    $toolkit =& new MockLimbToolkit($this);
    $request =& new MockRequest($this);

    $toolkit->setReturnReference('getRequest', $request);
    Limb :: registerToolkit($toolkit);

    $request->setReturnValue('getUri', $uri);

    $template = '<limb:pager:NAVIGATOR id="test" items="5">'.
                '{$TotalItems}|{$TotalPages}|' .
                '<core:OPTIONAL for="HasMoreThanOnePage">yes|</core:OPTIONAL>' .
                'from:{$BeginItemNumber}|to:{$EndItemNumber}' .
                '</limb:pager:NAVIGATOR>';

    RegisterTestingTemplate('/limb/pager_props.html', $template);

    $page =& new Template('/limb/pager_props.html');

    $navigator =& $page->findChild('test');

    $request->setReturnValue('get', 2, array($navigator->getPagerId()));

    $navigator->setTotalItems(40);
    $navigator->prepare();

    $expected = '40|8|yes|from:6|to:10';

    $this->assertEqual($page->capture(), $expected);

    Limb :: restoreToolkit();
  }

  function testSinglePage()
  {
    $uri = new Uri('test.com');

    $toolkit =& new MockLimbToolkit($this);
    $request =& new MockRequest($this);

    $toolkit->setReturnReference('getRequest', $request);
    Limb :: registerToolkit($toolkit);

    $request->setReturnValue('getUri', $uri);

    $template = '<limb:pager:NAVIGATOR id="test" items="10">'.
                '<limb:pager:FIRST>F-{$href}|</limb:pager:FIRST>' .
                '<limb:pager:PREV>P-{$href}|</limb:pager:PREV>' .
                '<limb:pager:LIST>'.
                '<limb:pager:CURRENT>C-{$href}|{$number}|</limb:pager:CURRENT>' .
                '<limb:pager:NUMBER>N-{$href}|{$number}|</limb:pager:NUMBER>' .
                '</limb:pager:LIST>'.
                '<limb:pager:NEXT>X-{$href}|</limb:pager:NEXT>' .
                '<limb:pager:LAST>L-{$href}|</limb:pager:LAST>' .
                '</limb:pager:NAVIGATOR>';

    RegisterTestingTemplate('/limb/pager_one_page_only.html', $template);

    $page =& new Template('/limb/pager_one_page_only.html');

    $navigator =& $page->findChild('test');
    $navigator->setTotalItems(5);

    $navigator->prepare();

    $expected = '';

    $this->assertEqual($page->capture(), $expected);

    Limb :: restoreToolkit();
  }

  function testFistPage()
  {
    $uri = new Uri('test.com');

    $toolkit =& new MockLimbToolkit($this);
    $request =& new MockRequest($this);

    $toolkit->setReturnReference('getRequest', $request);
    Limb :: registerToolkit($toolkit);

    $request->setReturnValue('getUri', $uri);
    $request->setReturnValue('export', array());

    $template = '<limb:pager:NAVIGATOR id="test" items="10">'.
                '<limb:pager:FIRST>F-{$href}|</limb:pager:FIRST>' .
                '<limb:pager:PREV>P-{$href}|</limb:pager:PREV>' .
                '<limb:pager:LIST>'.
                '<limb:pager:CURRENT>C-{$href}|{$number}|</limb:pager:CURRENT>' .
                '<limb:pager:NUMBER>N-{$href}|{$number}|</limb:pager:NUMBER>' .
                '</limb:pager:LIST>'.
                '</limb:pager:NAVIGATOR>';

    RegisterTestingTemplate('/limb/pager_first_page.html', $template);

    $page =& new Template('/limb/pager_first_page.html');

    $navigator =& $page->findChild('test');
    $navigator->setTotalItems(40);

    $navigator->prepare();

    $expected = 'C-test.com|1|'.
                'N-test.com?page_test=2|2|N-test.com?page_test=3|3|N-test.com?page_test=4|4|';

    $this->assertEqual($page->capture(), $expected);

    Limb :: restoreToolkit();
  }

  function testLastPage()
  {
    $uri = new Uri('test.com');

    $toolkit =& new MockLimbToolkit($this);
    $request =& new MockRequest($this);

    $toolkit->setReturnReference('getRequest', $request);
    Limb :: registerToolkit($toolkit);

    $request->setReturnValue('getUri', $uri);
    $request->setReturnValue('export', array());

    $template = '<limb:pager:NAVIGATOR id="test" items="10">'.
                '<limb:pager:FIRST>F-{$href}|</limb:pager:FIRST>' .
                '<limb:pager:PREV>P-{$href}|</limb:pager:PREV>' .
                '<limb:pager:LIST>'.
                '<limb:pager:CURRENT>C-{$href}|{$number}|</limb:pager:CURRENT>' .
                '<limb:pager:NUMBER>N-{$href}|{$number}|</limb:pager:NUMBER>' .
                '</limb:pager:LIST>'.
                '</limb:pager:NAVIGATOR>';

    RegisterTestingTemplate('/limb/pager_last_page.html', $template);

    $page =& new Template('/limb/pager_last_page.html');

    $navigator =& $page->findChild('test');

    $request->setReturnValue('export', array($navigator->getPagerId() => 3));
    $request->setReturnValue('get', 3, array($navigator->getPagerId()));

    $navigator->setTotalItems(30);
    $navigator->prepare();

    $expected = 'F-test.com|' .
                'P-test.com?page_test=2|'.
                'N-test.com|1|'.
                'N-test.com?page_test=2|2|'.
                'C-test.com?page_test=3|3|';

    $this->assertEqual($page->capture(), $expected);

    Limb :: restoreToolkit();
  }


  function testSections()
  {
    $uri = new Uri('test.com');

    $toolkit =& new MockLimbToolkit($this);
    $request =& new MockRequest($this);

    $toolkit->setReturnReference('getRequest', $request);
    Limb :: registerToolkit($toolkit);

    $request->setReturnValue('getUri', $uri);

    $template = '<limb:pager:NAVIGATOR id="test" items="10" pages_per_section="2">'.
                '<limb:pager:LIST>'.
                '<limb:pager:CURRENT>C-{$href}|{$number}|</limb:pager:CURRENT>' .
                '<limb:pager:NUMBER>N-{$href}|{$number}|</limb:pager:NUMBER>' .
                '<limb:pager:SECTION>S-{$href}|{$number_begin}|{$number_end}|</limb:pager:SECTION>' .
                '</limb:pager:LIST>'.
                '</limb:pager:NAVIGATOR>';

    RegisterTestingTemplate('/limb/pager_sections.html', $template);

    $page =& new Template('/limb/pager_sections.html');

    $navigator =& $page->findChild('test');

    $request->setReturnValue('export', array($navigator->getPagerId() => 3));
    $request->setReturnValue('get', 3, array($navigator->getPagerId()));

    $navigator->setTotalItems(60);
    $navigator->prepare();

    $expected = 'S-test.com?page_test=2|1|2|' .
                'C-test.com?page_test=3|3|'.
                'N-test.com?page_test=4|4|'.
                'S-test.com?page_test=5|5|6|';

    $this->assertEqual($page->capture(), $expected);

    Limb :: restoreToolkit();
  }

  function testMirror()
  {
    $uri = new Uri('test.com');

    $toolkit =& new MockLimbToolkit($this);
    $request =& new MockRequest($this);

    $toolkit->setReturnReference('getRequest', $request);
    Limb :: registerToolkit($toolkit);

    $request->setReturnValue('getUri', $uri);

    $template = '<limb:pager:NAVIGATOR id="test1" items="10">'.
                '<limb:pager:PREV>P1-{$href}|</limb:pager:PREV>' .
                '</limb:pager:NAVIGATOR>'. //note, mirror settings override source!!!
                '<limb:pager:NAVIGATOR id="test2" mirror="test1" items="30">'.
                '<limb:pager:PREV>P2-{$href}|</limb:pager:PREV>' .
                '</limb:pager:NAVIGATOR>';

    RegisterTestingTemplate('/limb/pager_navigator_mirror.html', $template);

    $page =& new Template('/limb/pager_navigator_mirror.html');

    $navigator =& $page->findChild('test1');

    $pager_id = $navigator->getPagerId();
    $request->setReturnValue('export', array($pager_id => 3));
    $request->setReturnValue('get', 3, array($pager_id));

    $navigator->setTotalItems(60);
    $navigator->prepare();

    $expected = "P1-test.com|P2-test.com|";
    $this->assertEqual($page->capture(), $expected);

    Limb :: restoreToolkit();
  }

}
?>

