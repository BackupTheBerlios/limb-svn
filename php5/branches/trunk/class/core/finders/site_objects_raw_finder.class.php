<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/finders/data_finder.interface.php');

class site_objects_raw_finder implements data_finder
{
  const RAW_SELECT_STMT = 
    "SELECT
    sso.current_version as current_version,
    sso.modified_date as modified_date,
    sso.status as status,
    sso.created_date as created_date,
    sso.creator_id as creator_id,
    sso.locale_id as locale_id,
    %s
    sso.title as title,
    sso.identifier as identifier,
    sso.id as id,
    ssot.id as node_id,
    ssot.parent_id as parent_node_id,
    ssot.level as level,
    ssot.priority as priority,
    ssot.children as children,
    sso.current_version as version,
    sys_class.id as class_id,
    sys_class.name as class_name,
    sys_behaviour.id as behaviour_id,
    sys_behaviour.name as behaviour,
    sys_behaviour.icon as icon,
    sys_behaviour.sort_order as sort_order,
    sys_behaviour.can_be_parent as can_be_parent
    FROM
    sys_site_object as sso, sys_class, sys_behaviour,
    sys_site_object_tree as ssot
    %s
    WHERE sys_class.id = sso.class_id
    AND sys_behaviour.id = sso.behaviour_id
    AND ssot.object_id = sso.id
    %s %s";
    
  const RAW_COUNT_STMT = 
    "SELECT COUNT(sso.id) as count
     FROM sys_site_object as sso %s
     WHERE sso.id %s %s";
  
  public function find($params = array(), $sql_params=array())//refactor!!!
  {
    $sql = sprintf(self :: RAW_SELECT_STMT,
                  $this->_add_sql($sql_params, 'columns'),
                  $this->_add_sql($sql_params, 'tables'),
                  $this->_add_sql($sql_params, 'conditions'),
                  $this->_add_sql($sql_params, 'group'));

    if(isset($params['order']))
      $sql .= ' ORDER BY ' . $this->_build_order_sql($params['order']);

    $db = Limb :: toolkit()->getDB();

    $limit = isset($params['limit']) ? $params['limit'] : 0;
    $offset = isset($params['offset']) ? $params['offset'] : 0;

    $db->sql_exec($sql, $limit, $offset);

    return $db->get_array('id');
  }
  
  public function find_by_id($id)
  {
    return $this->find(array(), array('conditions' => array(' AND sso.id=' . $id)));
  }
  
  protected function _add_sql($add_sql, $type)//refactor!!!
  {
    if (isset($add_sql[$type]))
      return implode(' ', $add_sql[$type]);
    else
      return '';
  }  

  protected function _build_order_sql($order_array)
  {
    $columns = array();

    foreach($order_array as $column => $sort_type)
      $columns[] = $column . ' ' . $sort_type;

    return implode(', ', $columns);
  }

  public function count($sql_params=array())//refactor!!!
  {
    $sql = sprintf(self :: RAW_COUNT_STMT,
                  $this->_add_sql($sql_params, 'tables'),
                  $this->_add_sql($sql_params, 'conditions'),
                  $this->_add_sql($sql_params, 'group')
                );

    $db = Limb :: toolkit()->getDB();

    $db->sql_exec($sql);

    if (!isset($sql_params['group']))
    {
      $arr = $db->fetch_row();
      return (int)$arr['count'];
    }
    else
      return $db->count_selected_rows();
  }
}

?>
