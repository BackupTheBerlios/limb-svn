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
require_once(LIMB_DIR . '/class/data_mappers/AbstractDataMapper.class.php');

class SiteObjectMapper extends AbstractDataMapper
{
  function & _createDomainObject()
  {
    include_once(LIMB_DIR . '/class/site_objects/SiteObject.class.php');
    return new SiteObject();
  }

  function & _getFinder()
  {
    include_once(LIMB_DIR . '/class/finders/FinderFactory.class.php');
    return FinderFactory :: create('site_objects_raw_finder');
  }

  function _doLoad($raw_data, &$site_object)
  {
    $site_object->import($raw_data);

    $this->_doLoadBehaviour($raw_data, $site_object);
  }

  function _doLoadBehaviour($raw_data, &$site_object)
  {
    $mapper =& $this->_getBehaviourMapper();
    $behaviour =& $mapper->findById($raw_data['behaviour_id']);

    $site_object->attachBehaviour($behaviour);
  }

  function &_getBehaviourMapper()
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->createDataMapper('SiteObjectBehaviourMapper');
  }

  function insert(&$site_object)
  {
    $id = $this->_insertSiteObjectRecord($site_object);

    $node_id = $this->_insertTreeNode($site_object);

    $site_object->setId($id);
    $site_object->setNodeId($node_id);

    return $id;
  }

  function _insertTreeNode(&$site_object)
  {
    if(!($parent_node_id = $site_object->getParentNodeId()))
      return throw(new LimbException('tree parent node is empty'));

    if(!$this->_canAddNodeToParent($parent_node_id))
      return throw(new LimbException('tree registering failed', array('parent_node_id' => $parent_node_id)));

    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    $values['identifier'] = $site_object->getIdentifier();
    $values['object_id'] = $site_object->getId();

    if (!$node_id = $tree->createSubNode($parent_node_id, $values))
      return throw(new LimbException('could not create tree node'));

    return $node_id;
  }

  function _insertSiteObjectRecord(&$site_object)
  {
    $gntr =& $this->_getIdentifierGenerator();

    if(!$identifier = $gntr->generate($site_object))
      return throw(new LimbException('identifier is empty'));

    if (!$behaviour = $site_object->getBehaviour())
      return throw(new LimbException('behaviour is not attached'));

    if (!$class_id = $this->getClassId($site_object))
      return throw(new LimbException('class id is empty'));

    if(!$created_date = $site_object->getCreatedDate())
      $site_object->setCreatedDate(time());

    if(!$modified_date = $site_object->getModifiedDate())
      $site_object->setModifiedDate(time());

    if (!$site_object->getLocaleId())
      $site_object->setLocaleId($this->getParentLocaleId($site_object->getParentNodeId()));

    $site_object->setVersion(1);

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();

    $mapper =& $this->_getBehaviourMapper();
    $mapper->save($site_object->getBehaviour());

    $site_object->setCreatorId($user->getId());

    $data['id'] = null;
    $data['identifier'] = $site_object->getIdentifier();
    $data['title'] = $site_object->getTitle();
    $data['class_id'] = $this->getClassId($site_object);

    $bhvr =& $site_object->getBehaviour();
    $data['behaviour_id'] = $bhvr->getId();

    $data['current_version'] = $site_object->getVersion();
    $data['creator_id'] = $site_object->getCreatorId();
    $data['status'] = $site_object->getStatus();
    $data['created_date'] = $site_object->getCreatedDate();
    $data['modified_date'] = $site_object->getModifiedDate();
    $data['locale_id'] = $site_object->getLocaleId();

    $sys_site_object_db_table = $toolkit->createDBTable('SysSiteObject');

    $sys_site_object_db_table->insert($data);

    if(catch('Exception', $e))
      return throw($e);

    return $sys_site_object_db_table->getLastInsertId();
  }

  function update(&$site_object)
  {
    $this->_updateTreeNode($site_object);

    $this->_updateSiteObjectRecord($site_object);
  }

  function _updateSiteObjectRecord(&$site_object)
  {
    if(!$site_object->getId())
      return throw(new LimbException('object id not set'));

    if(!$site_object->getIdentifier())
      return throw(new LimbException('identifier is empty'));

    if (!$site_object->getBehaviour())
      return throw(new LimbException('behaviour id not attached'));

    $mapper =& $this->_getBehaviourMapper();
    $mapper->save($site_object->getBehaviour());

    $data['current_version'] = $site_object->getVersion();

    $bhvr =& $site_object->getBehaviour();
    $data['behaviour_id'] = $bhvr->getId();

    $data['locale_id'] = $site_object->getLocaleId();
    $data['modified_date'] = $site_object->getModifiedDate();
    $data['identifier'] = $site_object->getIdentifier();
    $data['title'] = $site_object->getTitle();
    $data['status'] = $site_object->getStatus();

    $toolkit =& Limb :: toolkit();
    $sys_site_object_db_table =& $toolkit->createDBTable('SysSiteObject');
    return $sys_site_object_db_table->updateById($site_object->getId(), $data);
  }

  function _updateTreeNode(&$site_object)
  {
    if(!$site_object->getNodeId())
      return throw(new LimbException('node id not set'));

    if(!$site_object->getParentNodeId())
      return throw(new LimbException('parent node id not set'));

    $node_id = $site_object->getNodeId();
    $parent_node_id = $site_object->getParentNodeId();
    $identifier = $site_object->getIdentifier();

    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();
    $node = $tree->getNode($node_id);

    if ($this->_isObjectMovedFromNode($parent_node_id, $node))
    {
      if(!$this->_canAddNodeToParent($parent_node_id))
        return throw(new LimbException('new parent cant accept children',
                                array('parent_node_id' => $parent_node_id)));

      if (!$tree->moveTree($node_id, $parent_node_id))
      {
        return throw(new LimbException('could not move node',
          array(
            'node_id' => $node_id,
            'target_id' => $parent_node_id,
          )
        ));
      }
    }

    if ($identifier != $node['identifier'])
      return $tree->updateNode($node_id, array('identifier' => $identifier), true);

    return true;
  }

  function &_getIdentifierGenerator()
  {
    include_once(LIMB_DIR . '/class/data_mappers/DefaultSiteObjectIdentifierGenerator.class.php');
    return new DefaultSiteObjectIdentifierGenerator();
  }

  function _canAddNodeToParent($parent_node_id)
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    return $tree->canAddNode($node_id);
  }

  function getClassId($site_object)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysClass');

    $class_name = get_class($site_object);

    $list = $db_table->getList('name="'. $class_name. '"');

    if (count($list) == 1)
    {
      return key($list);
    }
    elseif(count($list) > 1)
    {
      return throw(new LimbException('there are more than 1 type found',
        array('name' => $class_name)));
    }

    $insert_data['id'] = null;
    $insert_data['name'] = $class_name;

    $db_table->insert($insert_data);

    return $db_table->getLastInsertId();
  }

  function _isObjectMovedFromNode($parent_node_id, $node)
  {
    return ($node['parent_id'] != $parent_node_id);
  }

  function delete(&$site_object)
  {
    if (!$this->canDelete($site_object))
      return;

    $this->_deleteTreeNode($site_object);

    $this->_deleteSiteObjectRecord($site_object);
  }

  function _deleteTreeNode(&$site_object)
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();
    $tree->deleteNode($site_object->getNodeId());
  }

  function _deleteSiteObjectRecord(&$site_object)
  {
    $toolkit =& Limb :: toolkit();
    $sys_site_object_db_table =& $toolkit->createDBTable('SysSiteObject');
    $sys_site_object_db_table->deleteById($site_object->getId());
  }

  function canDelete(&$site_object)
  {
    if(!$this->_canDeleteSiteObjectRecord($site_object))
      return false;

    return $this->_canDeleteTreeNode($site_object);
  }

  function _canDeleteTreeNode(&$site_object)
  {
    if(!$site_object->getNodeId())
      return throw(new LimbException('node id not set'));

    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    return $tree->canDeleteNode($site_object->getNodeId());
  }

  function _canDeleteSiteObjectRecord(&$site_object)
  {
    if(!$site_object->getId())
      return throw(new LimbException('object id not set'));

    return true;
  }

  function getParentLocaleId($parent_node_id)
  {
    $sql = "SELECT sso.locale_id as locale_id
            FROM sys_site_object as sso, sys_site_object_tree as ssot
            WHERE ssot.id = {$parent_node_id}
            AND sso.id = ssot.object_id";

    $toolkit =& Limb :: toolkit();
    $db =& $toolkit->getDB();

    $db->sqlExec($sql);

    $parent_data = $db->fetchRow();

    if (isset($parent_data['locale_id']) &&  $parent_data['locale_id'])
      return $parent_data['locale_id'];
    else
      return $toolkit->constant('DEFAULT_CONTENT_LOCALE_ID');
  }

}

?>
