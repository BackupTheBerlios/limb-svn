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

class LimbJIPDatasourceProcessorTagsTestCase extends LimbTestCase
{
  function LimbJIPDatasourceProcessorTagsTestCase()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testTag()
  {
    $data = array('_node_path' => $path1 = '/cms/limb/',
                  'actions' => array('create' => array('jip' => true),
                                      'edit' => array('jip' => true),
                                      'display' => array(),
                                      'delete' => array('jip' => true)));

    $template = '<core:DATASOURCE id="realm"><limb:datasource_processor:JIP>'.
                '<list:LIST from="jip_actions"><list:ITEM>'.
                '<core:OPTIONAL for="jip">{$name}-{$jip_href}|</core:OPTIONAL>'.
                '</list:ITEM></list:LIST>'.
                '</core:DATASOURCE>';

    RegisterTestingTemplate('/limb/jip_datasource_processor.html', $template);

    $page =& new Template('/limb/jip_datasource_processor.html');
    $component =& $page->findChild('realm');

    $dataspace = new Dataspace();
    $dataspace->import($data);
    $component->registerDataSource($dataspace);

    $this->assertEqual($page->capture(), 'create-/cms/limb/?action=create|'.
                                         'edit-/cms/limb/?action=edit|'.
                                         'delete-/cms/limb/?action=delete|');
  }

}
?>
