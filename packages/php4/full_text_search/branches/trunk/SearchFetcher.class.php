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
require_once(LIMB_DIR . '/core/search/FullTextSearch.class.php');

class SearchFetcher
{
  var $_query_object;

  function & instance()
  {
    if (!isset($GLOBALS['SearchFetcherGlobalInstance']) || !is_a($GLOBALS['SearchFetcherGlobalInstance'], 'SearchFetcher'))
      $GLOBALS['SearchFetcherGlobalInstance'] =& new SearchFetcher();

    return $GLOBALS['SearchFetcherGlobalInstance'];
  }

  function setSearchQueryObject($query_object)
  {
    $this->_query_object = $query_object;
  }

  function _getClassesIdsFromString($classes_string)
  {
    $classes_ids = array();
    $classes_names = explode(',', $classes_string);
    foreach($classes_names as $class_name)
    {
      if(trim($class_name))
      {
        $toolkit =& Limb :: toolkit();
        $site_object =& $toolkit->createSiteObject(trim($class_name));
        $classes_ids[] = $site_object->getClassId();
      }
    }

    return $classes_ids;
  }

  function searchFetch($loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_accessible_by_ids')
  {
    if (!$this->_query_object)
      return array();

    $toolkit =& Limb :: toolkit();
    $site_object =& $toolkit->createSiteObject($loader_class_name);

    $restricted_classes = array();
    $allowed_classes = array();

    if (!isset($params['restrict_by_class']) ||
        (isset($params['restrict_by_class']) &&  (bool)$params['restrict_by_class']))
      $class_id = $site_object->getClassId();
    else
    {
      $class_id = null;

      if(isset($params['restricted_classes']))
        $restricted_classes = $this->_getClassesIdsFromString($params['restricted_classes']);
      if(isset($params['allowed_classes']))
        $allowed_classes = $this->_getClassesIdsFromString($params['allowed_classes']);

    }

    $search = new FullTextSearch();

    $search_result = $search->find($this->_query_object, $class_id, $restricted_classes, $allowed_classes);
    if (!count($search_result))
      return array();

    $counter = 0;
    $count_method = $fetch_method . '_count';

    $counter = $site_object->$count_method(array_keys($search_result), $params);
    $fetched_objects = $site_object->$fetch_method(array_keys($search_result), $params);

    if(!count($fetched_objects))
      return array();

    foreach($search_result as $key => $score)
    {
      if (isset($fetched_objects[$key]))
      {
        $result[$key] = $fetched_objects[$key];
        $result[$key]['score'] = $score;
      }
    }

    $toolkit =& Limb :: toolkit();
    $authr =& $toolkit->getAuthorizer();
    $authr->assignActionsToObjects($result);

    $this->_assignPaths($result);
    $this->_assignSearchPaths($result, isset($params['offset']) ? $params['offset'] : 0);

    return $result;
  }

  function searchFetchSubBranch($path, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch')
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();
    $site_object =& $toolkit()->createSiteObject($loader_class_name);

    if (!isset($params['restrict_by_class']) ||
        (isset($params['restrict_by_class']) &&  (bool)$params['restrict_by_class']))
      $class_id = $site_object->getClassId();
    else
      $class_id = null;

    if (isset($params['check_expanded_parents']))
      $check_expanded_parents = (bool)$params['check_expanded_parents'];
    else
      $check_expanded_parents = false;

    if (isset($params['include_parent']))
      $include_parent = (bool)$params['include_parent'];
    else
      $include_parent = false;

    $depth = isset($params['depth']) ? $params['depth'] : 1;

    if(!$nodes = $tree->getAccessibleSubBranchByPath($path, $depth, $include_parent, $check_expanded_parents, $class_id))
      return array();

    $object_ids = ComplexArray :: getColumnValues('object_id', $nodes);

    if (!count($object_ids))
      return array();

    return $this->searchFetchByIds($object_ids, $loader_class_name, $counter, $params, $fetch_method);
  }

  function searchFetchByIds($object_ids, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
  {
    if (!$this->_query_object)
      return array();

    $search = new FullTextSearch();
    $search_result = $search->findByIds($object_ids, $this->_query_object);

    if(!count($search_result))
      return array();

    $counter = 0;
    $count_method = $fetch_method . '_count';

    $toolkit =& Limb :: toolkit();

    $site_object = $toolkit->createSiteObject($loader_class_name);
    $counter = $site_object->$count_method(array_keys($search_result), $params);
    $fetched_objects = $site_object->$fetch_method(array_keys($search_result),$params);

    if(!count($fetched_objects))
      return array();

    foreach($search_result as $key => $score)
    {
      if (isset($fetched_objects[$key]))
      {
        $result[$key] = $fetched_objects[$key];
        $result[$key]['score'] = $score;
      }
    }

    $authr =& $toolkit->getAuthorizer();
    $authr->assignActionsToObjects($result);

    $this->_assignPaths($result);
    $this->_assignSearchPaths($result, isset($params['offset']) ? $params['offset'] : 0);

    return $result;
  }

  function _assignSearchPaths(& $objects_array, $offset = 0)
  {
    $query = $this->_query_object->toString();

    foreach($objects_array as $key => $data)
    {
      if(!isset($objects_array[$key]['title']) ||  !$objects_array[$key]['title'])
        $objects_array[$key]['title'] = $objects_array[$key]['path'];

      $objects_array[$key]['search_path'] = $objects_array[$key]['path'] . '?h=' . urlencode($query);
      $objects_array[$key]['search_full_path'] = 'http://' . $_SERVER['HTTP_HOST'] . $objects_array[$key]['path'];
    }
  }
}

?>