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
require_once(LIMB_DIR . '/core/template/components/datasource/SiteObjectComponent.class.php');
require_once(LIMB_DIR . '/core/datasources/SingleObjectDatasource.class.php');
require_once(LIMB_DIR . '/core/datasources/RequestedObjectDatasource.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');

Mock :: generate('LimbToolkit');
Mock :: generatePartial('SiteObjectComponent',
                        'SiteObjectComponentTestVersion',
                        array('import'));
Mock :: generate('Request');
Mock :: generate('SingleObjectDatasource');
Mock :: generate('RequestedObjectDatasource');

class SiteObjectComponentTest extends LimbTestCase
{
  var $component;
  var $datasource;
  var $toolkit;
  var $request;

  function SiteObjectComponentTest()
  {
    parent :: LimbTestCase('site object component test');
  }

  function setUp()
  {
    $this->toolkit = new MockLimbToolkit($this);

    $this->component = new SiteObjectComponentTestVersion($this);

    $this->request = new MockRequest($this);

    $this->toolkit->setReturnReference('getRequest', $this->request);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->request->tally();
    $this->toolkit->tally();
    $this->component->tally();

    Limb :: popToolkit();
  }

  function testFetchByPath()
  {
    $datasource = new MockSingleObjectDatasource($this);
    $this->toolkit->setReturnReference('getDatasource', $datasource, array('SingleObjectDatasource'));

    $datasource->expectOnce('setPath', array($path = '/root/test'));
    $datasource->expectOnce('fetch');

    $result = array('some_result');
    $datasource->setReturnValue('fetch', $result);

    $this->component->expectOnce('import', array($result));
    $this->component->fetchByPath($path);

    $datasource->tally();
  }

  function testFetchRequested()
  {
    $datasource = new MockRequestedObjectDatasource($this);
    $this->toolkit->setReturnReference('getDatasource', $datasource, array('RequestedObjectDatasource'));

    $datasource->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $datasource->expectOnce('fetch');

    $result = array('some_result');
    $datasource->setReturnReference('fetch', $result);

    $this->component->expectOnce('import', array($result));
    $this->component->fetchRequested();

    $datasource->tally();
  }
}

?>