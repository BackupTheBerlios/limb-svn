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
require_once(LIMB_DIR . '/class/core/permissions/User.class.php');
require_once(LIMB_DIR . '/class/core/controllers/SiteObjectController.class.php');
require_once(dirname(__FILE__) . '/../../template/components/MetadataComponent.class.php');

Mock :: generatePartial(
  'MetadataComponent',
  'MetadataComponentTestVersion',
  array('_getMappedController')
);

Mock :: generate('SiteObjectController');

class MetadataComponentTest extends LimbTestCase
{
  var $metadata_component = null;
  var $controller = null;

  var $parent_node_id = '';
  var $sub_node_id = '';
  var $sub_node_id2 = '';

  function setUp()
  {
    loadTestingDbDump(dirname(__FILE__) . '/../sql/metadata.sql');

    $this->metadata_component = new MetadataComponentTestVersion($this);
    $this->metadata_component->MetadataComponent();

    $this->controller = new MockSiteObjectController($this);

    $this->metadata_component->setReturnValue('_getMappedController', $this->controller);

    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    $values['identifier'] = 'object_300';
    $values['object_id'] = 300;
    $root_node_id = $tree->createRootNode($values, false, true);

    $values['identifier'] = 'object_301';
    $values['object_id'] = 301;
    $this->parent_node_id = $tree->createSubNode($root_node_id, $values);

    $values['identifier'] = 'object_302';
    $values['object_id'] = 302;
    $this->sub_node_id = $tree->createSubNode($this->parent_node_id, $values);

    $values['identifier'] = 'object_303';
    $values['object_id'] = 303;
    $this->sub_node_id2 = $tree->createSubNode($root_node_id, $values);
  }

  function tearDown()
  {
    clearTestingDbTables();

    $user =& User :: instance();
    $user->logout();

    $this->metadata_component->tally();
    $this->controller->tally();
  }

  function testGetCompleteMetadataComponentMetadata()
  {
    $this->metadata_component->setNodeId($this->sub_node_id);
    $this->metadata_component->loadMetadata();
    $this->assertEqual($this->metadata_component->getKeywords(), 'object_302_keywords');
    $this->assertEqual($this->metadata_component->getDescription(), 'object_302_description');
  }

  function testGetPartialObjectMetadata()
  {
    $this->metadata_component->setNodeId($this->parent_node_id);
    $this->metadata_component->loadMetadata();
    $this->assertEqual($this->metadata_component->getKeywords(), 'object_301_keywords');
    $this->assertEqual($this->metadata_component->getDescription(), 'object_300_description');
  }

  function testGetParentObjectMetadata()
  {
    $this->metadata_component->setNodeId($this->sub_node_id2);
    $this->metadata_component->loadMetadata();
    $this->assertEqual($this->metadata_component->getKeywords(), 'object_300_keywords');
    $this->assertEqual($this->metadata_component->getDescription(), 'object_300_description');
  }

  function testGetTitle()
  {
    $this->metadata_component->setNodeId($this->sub_node_id);
    $this->metadata_component->setTitleSeparator(' - ');

    $this->assertEqual($this->metadata_component->getTitle(), 'object_302_title - object_301_title - object_300_title');
  }

  function testGetBreadcrums()
  {
    $this->controller->expectOnce('getAction');
    $this->controller->setReturnValue('getAction', false);
    $this->controller->expectNever('getActionProperty');

    $this->metadata_component->setNodeId($this->sub_node_id);
    $breadcrumbs = $this->metadata_component->getBreadcrumbsDataset();

    $this->assertNull($breadcrumbs->get('is_last'));

    $paths = array('object_300', 'object_301', 'object_302');
    $path = '/';
    $breadcrumbs->reset();

    for($i=1; $i <= $breadcrumbs->getTotalRowCount(); $i++)
    {
      $breadcrumbs->next();
      $path .= current($paths) . '/';
      next($paths);
      $this->assertEqual($breadcrumbs->get('path'), $path);

      if ($i == $breadcrumbs->getTotalRowCount())
        $this->assertTrue($breadcrumbs->get('is_last'));
    }
  }

  function testGetBreadcrumsOffsetPath()
  {
    $this->controller->expectOnce('getAction');
    $this->controller->setReturnValue('getAction', false);
    $this->controller->expectNever('getActionProperty');

    $this->metadata_component->setNodeId($this->sub_node_id);
    $this->metadata_component->setOffsetPath('/object_300/object_301/');

    $breadcrumbs = $this->metadata_component->getBreadcrumbsDataset();

    $paths = array('object_302');
    $path = '/object_300/object_301/';
    $breadcrumbs->reset();

    for($i=1; $i <= $breadcrumbs->getTotalRowCount(); $i++)
    {
      $breadcrumbs->next();
      $path .= current($paths) . '/';
      next($paths);
      $this->assertEqual($breadcrumbs->get('path'), $path);

      if ($i == $breadcrumbs->getTotalRowCount())
        $this->assertTrue($breadcrumbs->get('is_last'));
    }
  }

  function testGetBreadcrumsWithAction()
  {
    $this->controller->expectOnce('getAction');
    $this->controller->setReturnValue('getAction', 'actionTest');
    $this->controller->setReturnValue('getActionProperty', true, array('action_test', 'display_in_breadcrumbs'));
    $this->controller->expectOnce('getDefaultAction');
    $this->controller->setReturnValue('getDefaultAction', 'defaultActionTest');
    $this->controller->expectOnce('getActionName');
    $this->controller->setReturnValue('getActionName', 'action Name', array('actionTest'));

    $this->metadata_component->setNodeId($this->sub_node_id);
    $breadcrumbs = $this->metadata_component->getBreadcrumbsDataset();

    $breadcrumbs->reset();
    while($breadcrumbs->next())
    {
      $path = $breadcrumbs->get('path');
      $title = $breadcrumbs->get('title');
    }

    $this->assertEqual('/object_300/object_301/object_302/?action=action_test', $path);
    $this->assertEqual('Action Name', $title);
  }

  function testGetBreadcrumsWithNoDefaultAction()
  {
    $this->controller->expectOnce('getAction');
    $this->controller->setReturnValue('getAction', 'actionTest');
    $this->controller->setReturnValue('getActionProperty', true, array('action_test', 'display_in_breadcrumbs'));
    $this->controller->expectOnce('getDefaultAction');
    $this->controller->setReturnValue('getDefaultAction', 'actionTest');
    $this->controller->expectNever('getActionName');

    $this->metadata_component->setNodeId($this->sub_node_id);
    $breadcrumbs = $this->metadata_component->getBreadcrumbsDataset();
  }

  function testGetBreadcrumsWithNoAction()
  {
    $this->controller->expectOnce('getAction');
    $this->controller->setReturnValue('getAction', 'actionTest');
    $this->controller->setReturnValue('getActionProperty', false, array('action_test', 'display_in_breadcrumbs'));
    $this->controller->expectNever('getDefaultAction');
    $this->controller->expectNever('getActionName');

    $this->metadata_component->setNodeId($this->sub_node_id);
    $breadcrumbs = $this->metadata_component->getBreadcrumbsDataset();
  }
}

?>