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
require_once(WACT_ROOT . '/iterator/arraydataset.inc.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');

class CountableDatasource// implements Datasource, Countable
{
  function fetch(){}
  function countTotal(){}
}

Mock :: generate('CountableDatasource');
Mock :: generate('LimbToolkit');
Mock :: generate('Request');

class LimbDatasourceTagTestCase extends LimbTestCase
{
  var $ds;
  var $toolkit;

  function LimbDatasourceTagTestCase()
  {
    parent :: LimbTestCase('limb datasource tag case');
  }

  function setUp()
  {
    $this->ds =& new MockCountableDatasource($this);
    $this->toolkit =& new MockLimbToolkit($this);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->ds->tally();
    $this->toolkit->tally();

    Limb :: popToolkit();

    ClearTestingTemplates();
  }

  function testSetupSingleTargetDatasource()
  {
    $this->toolkit->setReturnReference('getDatasource', $this->ds, array('TestDatasource'));

    $data = array (
      array ('username'=>'joe'),
      array ('username'=>'ivan'),
    );

    $dataset =& new ArrayDataSet($data);

    $this->ds->expectOnce('fetch');
    $this->ds->setReturnReference('fetch', $dataset);

    $template = '<limb:DATASOURCE target="testTarget" class="TestDatasource"></limb:DATASOURCE>' .
                '<list:LIST id="testTarget"><list:ITEM>{$username}</list:ITEM></list:LIST>';

    RegisterTestingTemplate('/limb/datasource.html', $template);

    $page =& new Template('/limb/datasource.html');

    $this->assertEqual($page->capture(), 'joeivan');
  }

  function testSetupMultipleTargetDatasource()
  {
    $this->toolkit->setReturnReference('getDatasource', $this->ds, array('TestDatasource'));

    $data = array (
      array ('username'=>'joe', 'secondname' => 'fisher'),
      array ('username'=>'ivan', 'secondname' => 'rush'),
    );

    $dataset =& new ArrayDataSet($data);

    $this->ds->expectOnce('fetch');
    $this->ds->setReturnReference('fetch', $dataset);

    $template = '<limb:DATASOURCE target="testTarget1,testTarget2" class="TestDatasource"></limb:DATASOURCE>' .
                '<list:LIST id="testTarget1"><list:ITEM>{$username}</list:ITEM></list:LIST>' .
                '<list:LIST id="testTarget2"><list:ITEM>{$secondname}</list:ITEM></list:LIST>';

    RegisterTestingTemplate('/limb/datasource2.html', $template);

    $page =& new Template('/limb/datasource2.html');

    $this->assertEqual($page->capture(), 'joeivanfisherrush');
  }

  function testSetupNavigatorDatasource()
  {
    $this->toolkit->setReturnReference('getDatasource', $this->ds, array('TestDatasource'));

    $data = array (
      array ('username'=>'joe'),
      array ('username'=>'ivan'),
    );

    $dataset =& new ArrayDataSet($data);

    $this->ds->expectOnce('fetch');
    $this->ds->setReturnReference('fetch', $dataset);

    $request =& new MockRequest($this);
    $this->toolkit->setReturnReference('getRequest', $request);

    $request->setReturnValue('getUri', new Uri('test.com'));

    $this->ds->expectOnce('countTotal');
    $this->ds->setReturnValue('countTotal', $total = 40);

    $template = '<limb:DATASOURCE target="testTarget" class="TestDatasource" navigator="pagenav"></limb:DATASOURCE>' .
                '<list:LIST id="testTarget"><list:ITEM>{$username}</list:ITEM></list:LIST>'.
                '<limb:pager:NAVIGATOR id="pagenav" items="10"></limb:pager:NAVIGATOR>';

    RegisterTestingTemplate('/limb/datasource3.html', $template);

    $page =& new Template('/limb/datasource3.html');

    $this->assertEqual($page->capture(), 'joeivan');

    $pager =& $page->findChild('pagenav');
    $this->assertEqual($pager->getTotalItems(), $total);
  }
}
?>
