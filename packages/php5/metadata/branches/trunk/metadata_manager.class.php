<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: stats_ip.class.php 659 2004-09-15 14:26:45Z pachanga $
*
***********************************************************************************/
class metadata_manager
{
  static public function save_metadata($object_id, $keywords, $description)
  {
    $sys_metadata_db_table = LimbToolsBox :: getToolkit()->createDBTable('sys_metadata');

    $sys_metadata_db_table->delete('object_id=' . $object_id);

    $metadata = array();
    $metadata['object_id'] = $object_id;
    $metadata['keywords'] = $keywords;
    $metadata['description'] = $description;

    $sys_metadata_db_table->insert($metadata);
    return $sys_metadata_db_table->get_last_insert_id();
  }

  static public function get_metadata($object_id)
  {
    $sys_metadata_db_table = LimbToolsBox :: getToolkit()->createDBTable('sys_metadata');
    $arr = $sys_metadata_db_table->get_list('object_id=' . $object_id);

    if (!count($arr))
      return array();

    return current($arr);
  }
}

?>