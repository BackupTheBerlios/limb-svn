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
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
require_once(LIMB_DIR . '/class/lib/util/ini_support.inc.php');

class FullTextSearch
{
  var $db;
  var $use_boolean_mode = false;

  function FullTextSearch()
  {
    $toolkit =& Limb :: toolkit();
    $this->db =& $toolkit->getDB();

    $this->use_boolean_mode = $this->_checkBooleanMode();
  }

  function _canPerformFulltextSearch()
  {
    $toolkit =& Limb :: toolkit();
    $ini =& $toolkit->getINI('common.ini');
    $db_type = $ini->getOption('type', 'DB');

    if($db_type == 'mysql')
    {
      $this->db->sqlExec('SELECT VERSION() as version');
      $row = $this->db->fetchRow();

      $version = explode('.', $row['version']);

      if((int)$version[0] > 3 ||  ((int)$version[0] == 3 &&  (int)$version[1] >= 23))
        return true;
    }
    return false;
  }

  function find($query, $class_id=null, $restricted_classes_ids = array(), $allowed_classes_ids = array())
  {
    if(!$this->_canPerformFulltextSearch())
      $result = array();

    if($query->isEmpty())
      return $result;

    $sql = $this->_getSearchSql($query);

    if (!$sql)
      return array();
    if($class_id !== null)
      $sql .= " AND class_id={$class_id}";
    else
    {
      if(count($restricted_classes_ids))
        $sql .= ' AND NOT(' . sqlIn('class_id', $restricted_classes_ids) . ')';
      if(count($allowed_classes_ids))
        $sql .= ' AND ' . sqlIn('class_id', $allowed_classes_ids);
    }

    return $this->_getDbResult($sql);
  }

  function findByIds($ids, $query)
  {
    $result = array();

    if($query->isEmpty())
      return $result;

    $sql = $this->_getSearchSql($query);

    $sql .= " AND " . sqlIn('object_id', $ids);

    return $this->_getDbResult($sql);
  }

  function _checkBooleanMode()
  {
    $toolkit =& Limb :: toolkit();
    $ini =& $toolkit->getINI('common.ini');
    $db_type = $ini->getOption('type', 'DB');

    if($db_type == 'mysql')
    {
      $this->db->sqlExec('SELECT VERSION() as version');
      $row = $this->db->fetchRow();

      if(($pos = strpos($row['version'], '.')) !== false)
      {
        $version = (int)substr($row['version'], 0, $pos);

        if($version > 3)
          return true;
      }
    }
    return false;
  }

  function _processQuery($query_object)
  {
    $query = '';

    $query_items = $query_object->getQueryItems();

    foreach($query_items as $key => $data)
      $query_items[$key] = $this->db->escape($data);

    if($this->use_boolean_mode)
    {
      $query = implode('* ', $query_items) . '*';
    }
    else
    {
      $query = implode(' ', $query_items);
    }

    return $query;
  }

  function _getSearchSql($query_object)
  {
    $query = $this->_processQuery($query_object);

    if(!$query)
      return '';

    $boolean_mode = '';
    if($this->use_boolean_mode)
      $boolean_mode = 'IN BOOLEAN MODE';

    $sql = sprintf('SELECT
                    object_id,
                    (MATCH (body) AGAINST ("%s" %s))*weight as score
                    FROM sys_full_text_index
                    WHERE MATCH (body) AGAINST ("%s" %s)',
                    $query,
                    $boolean_mode,
                    $query,
                    $boolean_mode
                  );

    return $sql;
  }

  function _getDbResult($sql)
  {
    $this->db->sqlExec($sql);

    $result = array();
    while($row = $this->db->fetchRow())
    {
      if(!isset($result[$row['object_id']]))
        $result[$row['object_id']] = $row['score'];
      else
        $result[$row['object_id']] += $row['score'];
    }

    arsort($result, SORT_NUMERIC);

    return $result;
  }

}

?>
