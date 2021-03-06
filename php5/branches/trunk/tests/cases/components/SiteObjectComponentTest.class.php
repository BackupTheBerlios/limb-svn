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
require_once(LIMB_DIR . '/class/template/components/datasource/SiteObjectComponent.class.php');
require_once(LIMB_DIR . '/class/template/Component.class.php');
require_once(LIMB_DIR . '/class/core/datasources/SingleObjectDatasource.class.php');
require_once(LIMB_DIR . '/class/core/datasources/RequestedObjectDatasource.class.php');
require_once(LIMB_DIR . '/class/core/request/Request.class.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');

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

  function setUp()
  {
    $this->toolkit = new MockLimbToolkit($this);

    $this->component = new SiteObjectComponentTestVersion($this);

    $this->request = new MockRequest($this);

    $this->toolkit->setReturnValue('getRequest', $this->request);

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
    $this->toolkit->setReturnValue('getDatasource', $datasource, array('SingleObjectDatasource'));

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
    $this->toolkit->setReturnValue('getDatasource', $datasource, array('RequestedObjectDatasource'));

    $datasource->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $datasource->expectOnce('fetch');

    $result = array('some_result');
    $datasource->setReturnValue('fetch', $result);

    $this->component->expectOnce('import', array($result));
    $this->component->fetchRequested();

    $datasource->tally();
  }
}

?>