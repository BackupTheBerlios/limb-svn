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
require_once(LIMB_DIR . '/class/core/data_mappers/abstract_data_mapper.class.php');

class site_object_mapper extends abstract_data_mapper 
{
  protected function _create_domain_object()
  {
    include_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
    return new site_object();
  }
  
  protected function _get_finder()
  {
    include_once(LIMB_DIR . '/class/core/finders/finder_factory.class.php');
    return finder_factory :: create('site_objects_raw_finder');
  }
  
  protected function _do_load($raw_data, $site_object)
  {
    $site_object->import($raw_data);
    
    $this->_do_load_behaviour($raw_data, $site_object);
  }
  
  protected function _do_load_behaviour($raw_data, $site_object)
  {
    $behaviour = $this->_get_behaviour_mapper()->find_by_id($raw_data['behaviour_id']);

    $site_object->attach_behaviour($behaviour);        
  }
  
  protected function _get_behaviour_mapper()
  {
    return Limb :: toolkit()->createDataMapper('site_object_behaviour_mapper');        
  }
  
  public function insert($site_object)
  {
    $id = $this->_insert_site_object_record($site_object);

    $node_id = $this->_insert_tree_node($site_object);

    $site_object->set_id($id);
    $site_object->set_node_id($node_id);

    return $id;    
  }
  
  protected function _insert_tree_node($site_object)
  {
    if(!($parent_node_id = $site_object->get_parent_node_id()))
      throw new LimbException('tree parent node is empty');

    if(!$this->_can_add_node_to_parent($parent_node_id))
      throw new LimbException('tree registering failed', array('parent_node_id' => $parent_node_id));

    $tree = Limb :: toolkit()->getTree();

    $values['identifier'] = $site_object->get_identifier();
    $values['object_id'] = $site_object->get_id();

    if (!$node_id = $tree->create_sub_node($parent_node_id, $values))
      throw new LimbException('could not create tree node');
    
    return $node_id;
  }
  
  protected function _insert_site_object_record($site_object)
  {
    if(!$identifier = $this->_get_identifier_generator()->generate($site_object))
      throw new LimbException('identifier is empty');

    if (!$behaviour = $site_object->get_behaviour())
      throw new LimbException('behaviour is not attached');
    
    if (!$class_id = $this->get_class_id($site_object))
      throw new LimbException('class id is empty');
    
    if(!$created_date = $site_object->get_created_date())
      $site_object->set_created_date(time());
    
    if(!$modified_date = $site_object->get_modified_date())
      $site_object->set_modified_date(time());

    if (!$site_object->get_locale_id())
      $site_object->set_locale_id($this->get_parent_locale_id($site_object->get_parent_node_id()));

    $site_object->set_version(1);

    $user = Limb :: toolkit()->getUser();
    
    $this->_get_behaviour_mapper()->save($site_object->get_behaviour());

    $site_object->set_creator_id($user->get_id());

    $data['id'] = null;
    $data['identifier'] = $site_object->get_identifier();
    $data['title'] = $site_object->get_title();
    $data['class_id'] = $this->get_class_id($site_object);
    $data['behaviour_id'] = $site_object->get_behaviour()->get_id();
    $data['current_version'] = $site_object->get_version();
    $data['creator_id'] = $site_object->get_creator_id();
    $data['status'] = $site_object->get_status();
    $data['created_date'] = $site_object->get_created_date();
    $data['modified_date'] = $site_object->get_modified_date();
    $data['locale_id'] = $site_object->get_locale_id();

    $sys_site_object_db_table = Limb :: toolkit()->createDBTable('sys_site_object');

    $sys_site_object_db_table->insert($data);

    return $sys_site_object_db_table->get_last_insert_id();
  }

  public function update($site_object)
  {
    $this->_update_tree_node($site_object);

    $this->_update_site_object_record($site_object);
  }

  protected function _update_site_object_record($site_object)
  {
    if(!$site_object->get_id())
      throw new LimbException('object id not set');

    if(!$site_object->get_identifier())
      throw new LimbException('identifier is empty');    

    if (!$site_object->get_behaviour())
      throw new LimbException('behaviour id not attached');   
    
    $this->_get_behaviour_mapper()->save($site_object->get_behaviour());    
    
    $data['current_version'] = $site_object->get_version();
    $data['behaviour_id'] = $site_object->get_behaviour()->get_id();
    $data['locale_id'] = $site_object->get_locale_id();
    $data['modified_date'] = $site_object->get_modified_date();
    $data['identifier'] = $site_object->get_identifier();
    $data['title'] = $site_object->get_title();
    $data['status'] = $site_object->get_status();

    $sys_site_object_db_table = Limb :: toolkit()->createDBTable('sys_site_object');
    $sys_site_object_db_table->update_by_id($site_object->get_id(), $data);
  }

  protected function _update_tree_node($site_object)
  {
    if(!$site_object->get_node_id())
      throw new LimbException('node id not set');

    if(!$site_object->get_parent_node_id())
      throw new LimbException('parent node id not set');
    
    $node_id = $site_object->get_node_id();
    $parent_node_id = $site_object->get_parent_node_id();
    $identifier = $site_object->get_identifier();
    
    $tree = Limb :: toolkit()->getTree();
    $node = $tree->get_node($node_id);
    
    if ($this->_is_object_moved_from_node($parent_node_id, $node))
    {
      if(!$this->_can_add_node_to_parent($parent_node_id))
        throw new LimbException('new parent cant accept children', 
                                array('parent_node_id' => $parent_node_id));
      
      if (!$tree->move_tree($node_id, $parent_node_id))
      {
        throw new LimbException('could not move node',
          array(
            'node_id' => $node_id,
            'target_id' => $parent_node_id,
          )
        );
      }
    }

    if ($identifier != $node['identifier'])
      $tree->update_node($node_id, array('identifier' => $identifier), true);
  }

  protected function _get_identifier_generator()
  {
    include_once(LIMB_DIR . '/class/core/data_mappers/default_site_object_identifier_generator.class.php');
    return new DefaultSiteObjectIdentifierGenerator();
  }

  protected function _can_add_node_to_parent($parent_node_id)
  {
    $tree = Limb :: toolkit()->getTree();

    return $tree->can_add_node($node_id);
  }

  public function get_class_id($site_object)
  {
    $db_table = Limb :: toolkit()->createDBTable('sys_class');

    $class_name = get_class($site_object);

    $list = $db_table->get_list('name="'. $class_name. '"');

    if (count($list) == 1)
    {
      return key($list);
    }
    elseif(count($list) > 1)
    {
      throw new LimbException('there are more than 1 type found',
        array('name' => $class_name));
    }

    $insert_data['id'] = null;
    $insert_data['name'] = $class_name;

    $db_table->insert($insert_data);

    return $db_table->get_last_insert_id();
  }

  protected function _is_object_moved_from_node($parent_node_id, $node)
  {
    return ($node['parent_id'] != $parent_node_id);
  }

  public function delete($site_object)
  {
    if (!$this->can_delete($site_object))
      return;

    $this->_delete_tree_node($site_object);

    $this->_delete_site_object_record($site_object);
  }

  protected function _delete_tree_node($site_object)
  {
    Limb :: toolkit()->getTree()->delete_node($site_object->get_node_id());
  }
  
  protected function _delete_site_object_record($site_object)
  {
    $sys_site_object_db_table = Limb :: toolkit()->createDBTable('sys_site_object');
    $sys_site_object_db_table->delete_by_id($site_object->get_id());
  }

  public function can_delete($site_object)
  {
    if(!$this->_can_delete_site_object_record($site_object))
      return false;

    return $this->_can_delete_tree_node($site_object);
  }

  protected function _can_delete_tree_node($site_object)
  {
    if(!$site_object->get_node_id())
      throw new LimbException('node id not set');
    
    return Limb :: toolkit()->getTree()->can_delete_node($site_object->get_node_id());
  }

  protected function _can_delete_site_object_record($site_object)
  {
    if(!$site_object->get_id())
      throw new LimbException('object id not set');
    
    return true;
  }

  public function get_parent_locale_id($parent_node_id)
  {
    $sql = "SELECT sso.locale_id as locale_id
            FROM sys_site_object as sso, sys_site_object_tree as ssot
            WHERE ssot.id = {$parent_node_id}
            AND sso.id = ssot.object_id";

    $db = Limb :: toolkit()->getDB();

    $db->sql_exec($sql);

    $parent_data = $db->fetch_row();

    if (isset($parent_data['locale_id']) && $parent_data['locale_id'])
      return $parent_data['locale_id'];
    else
      return Limb :: toolkit()->constant('DEFAULT_CONTENT_LOCALE_ID');
  }

}

?>
