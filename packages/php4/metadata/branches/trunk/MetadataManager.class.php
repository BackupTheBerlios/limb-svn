<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: stats_ip.class.php 659 2004-09-15 14:26:45Z pachanga $
*
***********************************************************************************/
class MetadataManager
{
  static public function saveMetadata($object_id, $keywords, $description)
  {
    $sys_metadata_db_table = Limb :: toolkit()->createDBTable('SysMetadata');

    $sys_metadata_db_table->delete('object_id=' . $object_id);

    $metadata = array();
    $metadata['object_id'] = $object_id;
    $metadata['keywords'] = $keywords;
    $metadata['description'] = $description;

    $sys_metadata_db_table->insert($metadata);
    return $sys_metadata_db_table->getLastInsertId();
  }

  static public function getMetadata($object_id)
  {
    $sys_metadata_db_table = Limb :: toolkit()->createDBTable('SysMetadata');
    $arr = $sys_metadata_db_table->getList('object_id=' . $object_id);

    if (!count($arr))
      return array();

    return current($arr);
  }
}

?>