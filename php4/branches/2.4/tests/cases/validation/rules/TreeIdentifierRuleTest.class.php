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
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(LIMB_DIR . '/core/validators/rules/TreeIdentifierRule.class.php');

class TreeIdentifierRuleTest extends SingleFieldRuleTestCase
{
  var $db = null;
  var $node_id_root;
  var $node_id_ru;
  var $node_id_document;
  var $node_id_doc1;
  var $node_id_doc2;

  function TreeIdentifierRuleTest()
  {
    parent :: SingleFieldRuleTestCase('tree identifier rule test');
  }

  function setUp()
  {
    parent :: setUp();

    $this->db =& new SimpleDb(LimbDbPool :: getConnection());

    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    $values['identifier'] = 'root';
    $this->node_id_root = $tree->createRootNode($values, false, true);

    $values['identifier'] = 'ru';
    $values['object_id'] = 1;
    $this->node_id_ru = $tree->createSubNode($this->node_id_root, $values);

    $values['identifier'] = 'document';
    $values['object_id'] = 10;
    $this->node_id_document = $tree->createSubNode($this->node_id_ru, $values);

    $values['identifier'] = 'doc1';
    $values['object_id'] = 20;
    $this->node_id_doc1 = $tree->createSubNode($this->node_id_ru, $values);

    $values['identifier'] = 'doc2';
    $values['object_id'] = 30;
    $this->node_id_doc2 = $tree->createSubNode($this->node_id_ru, $values);
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
    $this->validator->addRule(new TreeIdentifierRule('test', $this->node_id_ru, $this->node_id_document));

    $data = new Dataspace();
    $data->set('test', '');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testTreeIdentifierRuleNormal()
  {
    $this->validator->addRule(new TreeIdentifierRule('test', $this->node_id_ru, $this->node_id_document));

    $data = new Dataspace();
    $data->set('test', 'id_test');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testTreeIdentifierRuleError()
  {
    $this->validator->addRule(new TreeIdentifierRule('test', $this->node_id_ru, $this->node_id_document));

    $data = new Dataspace();
    $data->set('test', 'doc1');

    $this->ErrorList->expectOnce('addError', array('validation', 'ERROR_DUPLICATE_TREE_IDENTIFIER', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testTreeIdentifierSameNodeChangedIdentifier()
  {
    $this->validator->addRule(new TreeIdentifierRule('test', $this->node_id_ru, $this->node_id_doc1));

    $data = new Dataspace();
    $data->set('test', 'doc1');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testTreeIdentifierNodeIdNotSetError()
  {
    $this->validator->addRule(new TreeIdentifierRule('test', $this->node_id_ru));

    $data = new Dataspace();
    $data->set('test', 'doc1');

    $this->ErrorList->expectOnce('addError', array('validation', 'ERROR_DUPLICATE_TREE_IDENTIFIER', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }
}

?>