
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
require_once(LIMB_DIR . '/class/finders/OneTableObjectsRawFinder.class.php');

class ImageObjectsRawFinder extends OneTableObjectsRawFinder
{
  function _defineDbTableName()
  {
    return 'image_object';
  }

  function _doParentFind($params, $sql_params)
  {
    return parent :: find($params, $sql_params);
  }

  function find($params=array(), $sql_params=array())
  {
    if(!$records = $this->_doParentFind($params, $sql_params))
      return array();

    $images_ids = array();

    foreach($records as $record)
      $images_ids[] = "{$record['object_id']}";

    $ids = '('. implode(',', $images_ids) . ')';

    $sql = "SELECT
            iv.id as id,
            iv.image_id as image_id,
            iv.media_id as media_id,
            iv.variation as variation,
            iv.width as width,
            iv.height as height,
            m.media_file_id as media_file_id,
            m.size as size,
            m.mime_type as mime_type,
            m.file_name as file_name,
            m.etag as etag
            FROM image_variation iv, media m
            WHERE iv.media_id = m.id AND
            iv.image_id IN {$ids}";

    $t =& Limb :: toolkit();
    $db =& $t->getDB();

    $db->sqlExec($sql);

    if(!$images_variations = $db->getArray())
      return $records;

    foreach($images_variations as $variation_data)
    {
      foreach($records as $id => $record)
      {
        if($record['object_id'] == $variation_data['image_id'])
        {
          $records[$id]['variations'][$variation_data['variation']] = $variation_data;
          break;
        }
      }
    }

    return $records;
  }
}
?>

