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

class LimbActionsDatasourceProcessorTagsTestCase extends LimbTestCase
{
  function LimbActionsDatasourceProcessorTagsTestCase()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testTag()
  {
    $data = array('actions' => array('create' => array('jip' => true),
                                      'edit' => array('jip' => true),
                                      'display' => array(),
                                      'delete' => array('jip' => true)));

    $template = '<core:DATASOURCE id="realm"><limb:DSProcessor:GroupActions group_name="jip">'.
                '<list:LIST from="jip_actions"><list:ITEM>'.
                '<core:OPTIONAL for="jip">{$name}|</core:OPTIONAL>'.
                '</list:ITEM></list:LIST>'.
                '</core:DATASOURCE>';

    RegisterTestingTemplate('/limb/actions_DSProcessor.html', $template);

    $page =& new Template('/limb/actions_DSProcessor.html');
    $component =& $page->findChild('realm');

    $dataspace = new Dataspace();
    $dataspace->import($data);
    $component->registerDataSource($dataspace);

    $this->assertEqual($page->capture(), 'create|'.
                                         'edit|'.
                                         'delete|');
  }

}
?>
