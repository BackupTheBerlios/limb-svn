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
require_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/http/Uri.class.php');

class CountableDAO// implements DAO, Countable
{
  function fetch(){}
  function countTotal(){}
}

Mock :: generate('CountableDAO');
Mock :: generate('LimbBaseToolkit', 'MockLimbToolkit');
Mock :: generate('Request');

class LimbDAOTagTestCase extends LimbTestCase
{
  var $ds;
  var $toolkit;

  function LimbDAOTagTestCase()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->ds =& new MockCountableDAO($this);
    $this->toolkit =& new MockLimbToolkit($this);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->ds->tally();
    $this->toolkit->tally();

    Limb :: restoreToolkit();

    ClearTestingTemplates();
  }

  function testSingleTarget()
  {
    $this->toolkit->setReturnReference('createDAO', $this->ds, array('TestDAO'));

    $data = array (
      array ('username'=>'joe'),
      array ('username'=>'ivan'),
    );

    $dataset =& new PagedArrayDataSet($data);

    $this->ds->expectOnce('fetch');
    $this->ds->setReturnReference('fetch', $dataset);

    $template = '<limb:DAO target="testTarget" class="TestDAO"></limb:DAO>' .
                '<list:LIST id="testTarget"><list:ITEM>{$username}</list:ITEM></list:LIST>';

    RegisterTestingTemplate('/limb/dao.html', $template);

    $page =& new Template('/limb/dao.html');

    $this->assertEqual($page->capture(), 'joeivan');
  }

  function testMultipleTargets()
  {
    $this->toolkit->setReturnReference('createDAO', $this->ds, array('TestDAO'));

    $data = array (
      array ('username'=>'joe', 'secondname' => 'fisher'),
      array ('username'=>'ivan', 'secondname' => 'rush'),
    );

    $dataset =& new PagedArrayDataSet($data);

    $this->ds->expectOnce('fetch');
    $this->ds->setReturnReference('fetch', $dataset);

    $template = '<limb:DAO target="testTarget1,testTarget2" class="TestDAO"></limb:DAO>' .
                '<list:LIST id="testTarget1"><list:ITEM>{$username}</list:ITEM></list:LIST>' .
                '<list:LIST id="testTarget2"><list:ITEM>{$secondname}</list:ITEM></list:LIST>';

    RegisterTestingTemplate('/limb/dao2.html', $template);

    $page =& new Template('/limb/dao2.html');

    $this->assertEqual($page->capture(), 'joeivanfisherrush');
  }

  function testWithNavigator()
  {
    $this->toolkit->setReturnReference('createDAO', $this->ds, array('TestDAO'));

    $data = array (
      array ('username'=>'joe'),
      array ('username'=>'ivan'),
    );

    $dataset =& new PagedArrayDataSet($data);

    $this->ds->expectOnce('fetch');
    $this->ds->setReturnReference('fetch', $dataset);

    $request =& new MockRequest($this);
    $this->toolkit->setReturnReference('getRequest', $request);

    $request->setReturnValue('getUri', new Uri('test.com'));

    $template = '<limb:DAO target="testTarget" class="TestDAO" navigator="pagenav"></limb:DAO>' .
                '<list:LIST id="testTarget"><list:ITEM>{$username}</list:ITEM></list:LIST>'.
                '<limb:pager:NAVIGATOR id="pagenav" items="10"></limb:pager:NAVIGATOR>';

    RegisterTestingTemplate('/limb/dao3.html', $template);

    $page =& new Template('/limb/dao3.html');

    $this->assertEqual($page->capture(), 'joeivan');

    $pager =& $page->findChild('pagenav');
    $this->assertEqual($pager->getTotalItems(), 2);
  }
}
?>
