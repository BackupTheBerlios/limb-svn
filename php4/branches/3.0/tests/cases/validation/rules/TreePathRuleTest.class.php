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
require_once(WACT_ROOT . '/../tests/cases/validation/rules/singlefield.inc.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(LIMB_DIR . '/core/validators/rules/TreePathRule.class.php');

class TreePathRuleTest extends SingleFieldRuleTestCase
{
  var $db = null;
  var $node_id_root;
  var $node_id_document;

  function TreePathRuleTest()
  {
    parent :: SingleFieldRuleTestCase('tree path rule test');
  }

  function setUp()
  {
    parent :: setUp();

    $toolkit =& Limb :: toolkit();

    $this->db =& new SimpleDb($toolkit->getDbConnection());
    $tree =& $toolkit->getTree();

    $values['identifier'] = 'root';
    $this->node_id_root = $tree->createRootNode($values, false, true);

    $values['identifier'] = 'document';
    $values['object_id'] = 10;
    $this->node_id_document = $tree->createSubNode($this->node_id_root, $values);
  }

  function tearDown()
  {
    parent :: tearDown();
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_tree');
  }

  function testTreeIdentifierRuleBlank()
  {
    $this->validator->addRule(new TreePathRule('test'));

    $data = new Dataspace();
    $data->set('test', '');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testTreeIdentifierRuleNormal()
  {
    $this->validator->addRule(new TreePathRule('test'));

    $data = new Dataspace();
    $data->set('test', '/root/document');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testTreeIdentifierRuleError()
  {
    $this->validator->addRule(new TreePathRule('test'));

    $data = new Dataspace();
    $data->set('test', '/root/document/1');

    $this->ErrorList->expectOnce('addError', array('validation', 'ERROR_INVALID_TREE_PATH', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }
}

?>