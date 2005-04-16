<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbRepeatTagTest.class.php 1017 2005-01-13 12:10:15Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(LIMB_SIMPLE_ACL_DIR . 'SimpleACLBaseToolkit.class.php');
require_once(LIMB_SIMPLE_ACL_DIR . 'SimpleACLAuthorizer.class.php');

Mock :: generate('SimpleACLBaseToolkit');
Mock :: generate('SimpleACLAuthorizer');

class ActionsDatasourceProcessorTagsTestCase extends LimbTestCase
{
  var $toolkit;
  var $authorizer;

  function ActionsDatasourceProcessorTagsTestCase()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->toolkit = new MockSimpleACLBaseToolkit($this);
    $this->authorizer = new MockSimpleACLAuthorizer($this);

    $this->toolkit->setReturnReference('getAuthorizer', $this->authorizer);

    Limb :: registerToolkit($this->toolkit, 'SimpleACL');
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->authorizer->tally();

    ClearTestingTemplates();

    Limb :: restoreToolkit('SimpleACL');
  }

  function testTag()
  {
    $data = array('path' => $path = '/cms/limb/',
                  '_service_name' => $service_name = 'TestService');

    $this->authorizer->expectOnce('getAccessibleActions', array($path, $service_name));

    $actions = array('create' => array('name' => 'create'),
                     'edit' => array('name' => 'edit'));

    $this->authorizer->setReturnValue('getAccessibleActions', $actions);

    $template = '<core:DATASOURCE id="realm"><limb:datasource_processor:Actions>'.
                '<list:LIST from="actions"><list:ITEM>'.
                '{$name}|'.
                '</list:ITEM></list:LIST>'.
                '</core:DATASOURCE>';

    RegisterTestingTemplate('/limb/actions_datasource_processor.html', $template);

    $page =& new Template('/limb/actions_datasource_processor.html');
    $component =& $page->findChild('realm');

    $dataspace = new Dataspace();
    $dataspace->import($data);
    $component->registerDataSource($dataspace);

    $this->assertEqual($page->capture(), 'create|'.
                                         'edit|');
  }
}
?>
