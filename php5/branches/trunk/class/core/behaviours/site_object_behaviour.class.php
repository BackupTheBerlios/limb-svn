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
require_once(LIMB_DIR . '/class/i18n/strings.class.php');
	
class site_object_behaviour
{
  protected $_properties = array();

  protected $_behaviour_id;
  
  protected $_actions_list = array();

  function __construct()
  {
    $this->_properties = $this->_define_properties();
  }
  
  public function get_default_action()
  {
    return 'display';
  }
  
  public function get_actions_list()
  {
    if($this->_actions_list)
      return $this->_actions_list;
    
    $methods = get_class_methods($this);
    foreach($methods as $method)
    {
      if(preg_match('~^define_(.*)$~', $method, $matches))
        $this->_actions_list[] = $matches[1];
    }
    return $this->_actions_list;
  }
  
  public function action_exists($action)
  {
    return in_array($action, $this->get_actions_list());
  }

  protected function _define_properties()
  {
    return array(
      'sort_order' => 1,
      'can_be_parent' => 1,
      'icon' => '/shared/images/generic.gif',
    );
  }
  
  public function get_id()
  {
    $behaviour_name = get_class($this);
    
    $db_table = Limb :: toolkit()->createDBTable('sys_behaviour');
    $list = $db_table->get_list('name="'. $behaviour_name . '"');

    if (count($list) == 1)
    {
      return key($list);
    }
    elseif(count($list) > 1)
    {
      throw new LimbException('there are more than 1 behaviour found',
        __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
        array('behaviour_name' => $behaviour_name));
    }

    $insert_data = array();
    $insert_data['name'] = $behaviour_name;
    $insert_data['icon'] = $this->get_property('icon', '/shared/images/generic.gif');
    $insert_data['can_be_parent'] = $this->get_property('can_be_parent', 1);
    $insert_data['sort_order'] = $this->get_property('sort_order', 1);

    $db_table->insert($insert_data);

    return $db_table->get_last_insert_id();
  }   

  public function can_be_parent()
  {
    return $this->get_property('can_be_parent', false);
  }
  
  public function get_property($attribute_name, $default = null)
  {
    if(isset($this->_properties[$attribute_name]))
      return $this->_properties[$attribute_name];
    else
      return $default;
  }

  static public function can_accept_children($node_id)
  {
    $tree = Limb :: toolkit()->getTree();

    if (!$tree->can_add_node($node_id))
      return false;

    $sql = "SELECT sys_behaviour.name as behaviour_name
            FROM sys_site_object as sso, sys_behaviour, sys_site_object_tree as ssot
            WHERE ssot.id={$node_id}
            AND sso.behaviour_id=sys_behaviour.id
            AND sso.id=ssot.object_id";

    $db = Limb :: toolkit()->getDB();

    $db->sql_exec($sql);

    $row = $db->fetch_row();

    if (!is_array($row) || !count($row))
      return false;
    
    $behaviour = Limb :: toolkit()->createBehaviour($row['behaviour_name']);

    return $behaviour->can_be_parent();
  }
  
  static public function get_ids_by_names($names)
  {
    $db = Limb :: toolkit()->getDB();

    $db->sql_select('sys_behaviour', 'id', sql_in('name', $names));
    
    $result = array();
    while($row = $db->fetch_row())
      $result[] = $row['id'];
    
    return $result; 
  }
  
}

?>