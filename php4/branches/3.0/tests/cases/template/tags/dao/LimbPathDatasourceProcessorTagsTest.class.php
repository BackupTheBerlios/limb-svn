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
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');

Mock :: generate('Path2IdTranslator');
Mock :: generate('LimbBaseToolkit');

class LimbPathDatasourceProcessorTagsTestCase extends LimbTestCase
{
  var $toolkit;
  var $translator;

  function LimbPathDatasourceProcessorTagsTestCase()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->toolkit = new MockLimbBaseToolkit($this);
    $this->translator = new MockPath2IdTranslator($this);

    $this->toolkit->setReturnReference('getPath2IdTranslator', $this->translator);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->translator->tally();

    ClearTestingTemplates();

    Limb :: restoreToolkit();
  }

  function testTag()
  {
    $path = 'whatever';
    $data = array('_node_id' => $node_id = 10);

    $this->translator->expectOnce('getPathToNode', array($node_id));
    $this->translator->setReturnValue('getPathToNode', $path);

    $template = '<core:DATASOURCE id="realm"><limb:DSProcessor:Path>'.
                '{$_node_path}'.
                '</core:DATASOURCE>';

    RegisterTestingTemplate('/limb/path_DSProcessor.html', $template);

    $page =& new Template('/limb/path_DSProcessor.html');
    $component =& $page->findChild('realm');

    $dataspace = new Dataspace();
    $dataspace->import($data);
    $component->registerDataSource($dataspace);

    $this->assertEqual($page->capture(), $path);
  }

}
?>
