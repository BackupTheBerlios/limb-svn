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
require_once(LIMB_DIR . '/class/template/components/datasource/site_object_component.class.php');
require_once(LIMB_DIR . '/class/template/component.class.php');
require_once(LIMB_DIR . '/class/core/datasources/single_object_datasource.class.php');
require_once(LIMB_DIR . '/class/core/datasources/requested_object_datasource.class.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');

Mock :: generate('LimbToolkit');
Mock :: generatePartial('site_object_component',
                        'site_object_component_test_version',
                        array('import'));
Mock :: generate('request');
Mock :: generate('single_object_datasource');
Mock :: generate('requested_object_datasource');

class site_object_component_test extends LimbTestCase
{
  var $component;
  var $datasource;
  var $toolkit;
  var $request;

  function setUp()
  {
    $this->toolkit = new MockLimbToolkit($this);

    $this->component = new site_object_component_test_version($this);

    $this->request = new Mockrequest($this);

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

  function test_fetch_by_path()
  {
    $datasource = new Mocksingle_object_datasource($this);
    $this->toolkit->setReturnValue('getDatasource', $datasource, array('single_object_datasource'));

    $datasource->expectOnce('set_path', array($path = '/root/test'));
    $datasource->expectOnce('fetch');

    $result = array('some_result');
    $datasource->setReturnValue('fetch', $result);

    $this->component->expectOnce('import', array($result));
    $this->component->fetch_by_path($path);

    $datasource->tally();
  }

  function test_fetch_requested()
  {
    $datasource = new Mockrequested_object_datasource($this);
    $this->toolkit->setReturnValue('getDatasource', $datasource, array('requested_object_datasource'));

    $datasource->expectOnce('set_request', array(new IsAExpectation('Mockrequest')));
    $datasource->expectOnce('fetch');

    $result = array('some_result');
    $datasource->setReturnValue('fetch', $result);

    $this->component->expectOnce('import', array($result));
    $this->component->fetch_requested();

    $datasource->tally();
  }
}

?>