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
require_once(dirname(__FILE__) . '/SingleFieldRuleTest.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
require_once(LIMB_DIR . '/class/core/Dataspace.class.php');
require_once(LIMB_DIR . '/class/validators/rules/TreeIdentifierRule.class.php');

class TreeIdentifierRuleTest extends SingleFieldRuleTest
{
  var $db = null;
  var $node_id_root;
  var $node_id_ru;
  var $node_id_document;
  var $node_id_doc1;
  var $node_id_doc2;

  function setUp()
  {
    parent :: setUp();

    $this->db =& DbFactory :: instance();

    $tree = Limb :: toolkit()->getTree();

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
    $this->db->sqlDelete('sys_site_object_tree');
  }

  function testTreeIdentifierRuleBlank()
  {
    $this->validator->addRule(new TreeIdentifierRule('test', $this->node_id_ru, $this->node_id_document));

    $data = new Dataspace();
    $data->set('test', '');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testTreeIdentifierRuleNormal()
  {
    $this->validator->addRule(new TreeIdentifierRule('test', $this->node_id_ru, $this->node_id_document));

    $data = new Dataspace();
    $data->set('test', 'id_test');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testTreeIdentifierRuleError()
  {
    $this->validator->addRule(new TreeIdentifierRule('test', $this->node_id_ru, $this->node_id_document));

    $data = new Dataspace();
    $data->set('test', 'doc1');

    $this->error_list->expectOnce('addError', array('test', Strings :: get('error_duplicate_tree_identifier', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testTreeIdentifierSameNodeChangedIdentifier()
  {
    $this->validator->addRule(new TreeIdentifierRule('test', $this->node_id_ru, $this->node_id_doc1));

    $data = new Dataspace();
    $data->set('test', 'doc1');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testTreeIdentifierNodeIdNotSetError()
  {
    $this->validator->addRule(new TreeIdentifierRule('test', $this->node_id_ru));

    $data = new Dataspace();
    $data->set('test', 'doc1');

    $this->error_list->expectOnce('addError', array('test', Strings :: get('error_duplicate_tree_identifier', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }
}

?>