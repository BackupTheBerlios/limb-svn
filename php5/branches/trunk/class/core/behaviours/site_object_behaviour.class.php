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
	
abstract class site_object_behaviour
{
  protected  $_attributes_definition = array();

  protected  $_behaviour_properties = array();

  protected  $_behaviour_id;

  function __construct()
  {
    $this->_behaviour_properties = $this->_define_behaviour_properties();

    $this->_attributes_definition = $this->_define_attributes_definition();
  }

	protected function _define_attributes_definition()
	{
    return array();
	}

  protected function _define_behaviour_properties()
  {
    return array(
      'class_ordr' => 1,
      'can_be_parent' => 1,
      'icon' => '/shared/images/generic.gif',
    );
  }
  
  static public function get_id($behaviour_name)
  {
    $db_table = Limb :: toolkit()->createDBTable('sys_behaviour');
    $list = $db_table->get_list('name="'. $behaviour_name. '"');

    if (count($list) == 1)
    {
      return key($list);
    }
    elseif(count($list) > 1)
    {
      throw new LimbException('there are more than 1 behaviuor found',
        __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
        array('behaviour_name' => $behaviour_name));
    }

    $insert_data = array();
    $insert_data['name'] = $behaviour_name;
    
    $db_table->insert($insert_data);

    return $db_table->get_last_insert_id();
  }   

  public function can_be_parent()
  {
    if (isset($this->_behaviour_properties['can_be_parent']))
      return $this->_behaviour_properties['can_be_parent'];
    else
      return false;
  }
  
  public function get_attributes_definition()
  {
    return $this->_attributes_definition;
  }

  public function get_definition($attribute_name)
  {
    $definition = $this->get_attributes_definition();

    if(isset($definition[$attribute_name]))
    {
      if($definition[$attribute_name] == '')
        return array();
      else
        return $definition[$attribute_name];
    }
    return false;
  }

  public function can_accept_children($node_id)
  {
    $tree = Limb :: toolkit()->getTree();

    if (!$tree->can_add_node($node_id))
      return false;

    $sql = "SELECT sys_behaviour.name as behaviour_name
            FROM sys_site_object as sso, sys_behaviour, sys_site_object_tree as ssot
            WHERE ssot.id={$node_id}
            AND sso.behaviour_id=sys_behaviour.id
            AND sso.id=ssot.object_id";

    $db = db_factory :: instance();

    $db->sql_exec($sql);

    $row = $db->fetch_row();

    if (!is_array($row) || !count($row))
      return false;
    
    $behaviour = Limb :: toolkit()->createBehaviour($row['behaviour_name']);

    return $behaviour->can_be_parent();
  }
  
}

?>