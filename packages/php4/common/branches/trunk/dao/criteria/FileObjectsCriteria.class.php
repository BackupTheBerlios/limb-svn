
<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: FileObjectsRawFinder.class.php 1090 2005-02-03 13:07:57Z pachanga $
*
***********************************************************************************/
class FileObjectsCriteria
{
  function process(&$sql)
  {
    $sql->addTable('file_object');
    $sql->addField('file_object.id as file_object_id');
    $sql->addField('file_object.media_id as media_id');
    $sql->addCondition('file_object.oid = sys_object.oid');

    $sql->addTable('media as m');
    $sql->addCondition('file_object.media_id = m.id');
    $sql->addField('m.media_file_id as media_file_id');
    $sql->addField('m.file_name as file_name');
    $sql->addField('m.mime_type as mime_type');
    $sql->addField('m.etag as etag');
    $sql->addField('m.size as size');
  }
}
?>

