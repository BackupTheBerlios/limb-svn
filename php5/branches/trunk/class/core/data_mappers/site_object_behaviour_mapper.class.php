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

class site_object_behaviour_mapper
{
  public function save($behaviour)
  {
    if($behaviour->get_id())
      $this->update($behaviour);
    else
      $this->insert($behaviour);
  }
  
  public function insert($behaviour)
  {
    $table = Limb :: toolkit()->createDBTable('sys_behaviour');
    
    $data['name'] = get_class($behaviour);
    
    $table->insert($data);
    
    $id = $table->get_last_insert_id();
    
    $behaviour->set_id($id);

    return $id;    
  }
  
  public function update($behaviour)
  {
    if(!$id = $behaviour->get_id())
      throw new LimbException('id is not set');

    $table = Limb :: toolkit()->createDBTable('sys_behaviour');
    
    $data['name'] = get_class($behaviour);
    
    $table->update_by_id($id, $data);    
  }
  
  public function delete($behaviour)
  {
    if(!$id = $behaviour->get_id())
      throw new LimbException('id is not set');
    
    $table = Limb :: toolkit()->createDBTable('sys_behaviour');
    
    $table->delete_by_id($id);    
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
