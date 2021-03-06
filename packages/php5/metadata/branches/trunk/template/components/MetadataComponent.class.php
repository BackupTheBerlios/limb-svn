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
require_once(LIMB_DIR . '/class/template/Component.class.php');
require_once(LIMB_DIR . '/class/core/ArrayDataset.class.php');

class MetadataComponent extends Component
{
  protected $node_id = '';

  protected $request_path = '';

  protected $object_ids_array = array();

  protected $object_metadata = array();

  protected $separator = ' - ';

  protected $offset_path = '';

  protected $metadata_db_table_name = 'sys_metadata';
  protected $needed_metadata = array('keywords', 'description');

  protected function _getPathObjectsIdsArray()
  {
    if (count($this->object_ids_array))
      return $this->object_ids_array;

    $tree = Limb :: toolkit()->getTree();

    $node = $tree->getNode($this->getNodeId());
    $parents = $tree->getParents($this->getNodeId());

    $result = array();

    if (is_array($parents) &&  count($parents))
      foreach($parents as $parent_node)
      {
        $result[$parent_node['object_id']] = $parent_node['object_id'];
      }

    if ($node)
      $result[$node['object_id']] = $node['object_id'];

    return $this->object_ids_array = $result;
  }

  protected function  _getPathObjectsArray()
  {
    $ids_array = $this->_getPathObjectsIdsArray();

    $sql =
    'SELECT
    sso.id as id,
    sso.class_id as class_id,
    sso.current_version as current_version,
    sso.modified_date as modified_date,
    sso.identifier as identifier,
    sso.status as status,
    sso.created_date as created_date,
    sso.creator_id as creator_id,
    sso.locale_id as locale_id,
    sso.title as title
    FROM sys_site_object as sso, sys_site_object_tree as ssot
    WHERE ssot.object_id=sso.id
    AND	' . sqlIn('sso.id', $ids_array) . '
    ORDER BY ssot.level';

    $db = Limb :: toolkit()->getDB();
    $db->sqlExec($sql);

    return $db->getArray('id');
  }

  public function loadMetadata()
  {
    $ids_array = $this->_getPathObjectsIdsArray();

    if (!count($ids_array))
      return false;
    $ids_array = array_reverse($ids_array);

    $metadata_db_table	= Limb :: toolkit()->createDBTable($this->metadata_db_table_name);
    $objects_metadata = $metadata_db_table->getList(sqlIn('object_id', $ids_array), '', 'object_id');

    if (!count($objects_metadata))
      return false;

    $this->_processLoadedMetadata($ids_array, $objects_metadata);

    return true;
  }

  protected function _processLoadedMetadata($ids_array, $objects_metadata)
  {
    foreach($this->needed_metadata as $metadata_name)
      $metadata_loaded[$metadata_name] = false;

    foreach($ids_array as $object_id)
    {
      $can_stop_search = true;
      foreach($this->needed_metadata as $metadata_name)
        $can_stop_search = $can_stop_search &&  $metadata_loaded[$metadata_name];

      if ($can_stop_search)
        break;

      foreach($this->needed_metadata as $metadata_name)
        if (!$metadata_loaded[$metadata_name] &&  !empty($objects_metadata[$object_id][$metadata_name]))
        {
          $this->object_metadata[$metadata_name] = $objects_metadata[$object_id][$metadata_name];
          $metadata_loaded[$metadata_name] = true;
        }
    }
  }

  public function setNodeId($node_id)
  {
    $this->node_id = $node_id;
  }

  public function getNodeId()
  {
    if ($this->node_id)
      return $this->node_id;

    $toolkit = Limb :: toolkit();
    $request = $toolkit->getRequest();

    if($this->request_path)
    {
      $node_path = $request->get($this->request_path);
      $node = $toolkit->getTree()->getNodeByPath($node_path);
    }
    else
    {
      $datasource = Limb :: toolkit()->getDatasource('RequestedObjectDatasource');
      $datasource->setRequest($request);
      $node = $datasource->mapRequestToNode($request);
    }

    $this->node_id = $node['id'];

    return $this->node_id;
  }

  public function getKeywords()
  {
    return $this->get('keywords');
  }

  public function getDescription()
  {
    return $this->get('description');
  }

  public function get($name, $default_value = null)
  {
    if(isset($this->object_metadata[$name]))
      return $this->object_metadata[$name];
    else
      return $default_value;
  }

  public function setTitleSeparator($separator = ' ')
  {
    $this->separator = $separator;
  }

  public function getTitle()
  {
    $result = $this->_applyOffsetPath($this->_getPathObjectsArray());

    if (!is_array($result) ||  !count($result))
      return null;

    $titles = array();

    $objects_ids_array = array_reverse($this->_getPathObjectsIdsArray());
    foreach($objects_ids_array as $object_id)
      if (!empty($result[$object_id]['title']))
        $titles[] = $result[$object_id]['title'];

    if (!count($titles))
      return null;

    return implode($this->separator, $titles);
  }

  public function getBreadcrumbsDataset()
  {
    $objects_data = $this->_getPathObjectsArray();

    if (!is_array($objects_data) ||  !count($objects_data))
      return new ArrayDataset();

    $results = $this->_applyOffsetPath($objects_data);

    $this->_addObjectActionPath($results);

    $record = end($results);
    array_pop($results);

    $record['is_last'] = true;
    $results[-1] = $record;//???

    return new ArrayDataset($results);
  }

  protected function _applyOffsetPath($objects_data)
  {
    $path = '/';

    if($this->offset_path)
    {
      $offset_arr = explode('/', $this->offset_path);
      reset($offset_arr);
      array_shift($offset_arr);
    }

    $results = array();
    foreach($objects_data as $data)
    {
      $path .= $data['identifier'] . '/';
      $results[$data['id']] = array(
        'id' => $data['id'],
        'path' => $path,
        'title' => $data['title'] ? $data['title'] : $data['identifier']
      );

      if($this->offset_path &&  current($offset_arr))
      {
        if($data['identifier'] == current($offset_arr))
        {
          array_pop($results);
          next($offset_arr);
        }
      }

      $last_element = $data['id'];
    }

    return $results;
  }

  protected function _addObjectActionPath(&$results)
  {
    $data = end($results);
    $path = $data['path'];

    $controller = $this->_getMappedController();
    $request = Limb :: toolkit()->getRequest();
    $action = $controller->getAction($request);

    if ($action !== false && 
        $controller->getActionProperty($action, 'display_in_breadcrumbs') === true)
    {
      if($controller->getDefaultAction() != $action)
      {
        $results[] = array(
          'path' => $path .= '?action=' . $action,
          'title' => $controller->getActionName($action)
        );
      }
    }
  }

  protected function _getMappedController()
  {
    $request = Limb :: toolkit()->getRequest();
    return wrapWithSiteObject(Limb :: toolkit()->getFetcher()->fetchRequestedObject($request))->getController();
  }

  public function setOffsetPath($path)
  {
    $this->offset_path = $path;
  }

  public function setRequestPath($path)
  {
    $this->request_path = $path;
  }
}

?>