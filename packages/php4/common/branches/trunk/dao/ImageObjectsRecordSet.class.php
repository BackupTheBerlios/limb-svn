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
require_once(LIMB_DIR . '/core/db/IteratorDbDecorator.class.php');

class ImageObjectsRecordSet extends IteratorDbDecorator
{
  var $array_dataset;

  function valid()
  {
    return $this->array_dataset->valid();
  }

  function next()
  {
    $this->array_dataset->next();
  }

  function & current()
  {
    return $this->array_dataset->current();
  }

  function rewind()
  {
    parent :: rewind();
    $this->_loadVariations();
    $this->array_dataset->rewind();
  }

  function _loadVariations()
  {
    $this->array_dataset = new ArrayDataset(array());
    $cached_records = array();

    $ids = '';
    $records = array();
    for($this->iterator->rewind();$this->iterator->valid();$this->iterator->next())
    {
      $record =& $this->iterator->current();
      $id = $record->get('id');
      $ids .= $id . ',';
      $cached_records[$id] =& $record->export();
    }

    if(!$cached_records)
      return;

    $ids = rtrim($ids, ',');

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
            iv.image_id IN ({$ids})";

    $toolkit =& Limb :: toolkit();
    $conn =& $toolkit->getDbConnection();
    $stmt =& $conn->newStatement($sql);

    $rs =& $stmt->getRecordSet();

    for($rs->rewind();$rs->valid();$rs->next())
    {
      $variation = $rs->current();
      $variation_data = $variation->export();

      foreach(array_keys($cached_records) as $id)
      {
        if($id == $variation_data['image_id'])
        {
          $cached_records[$id]['variations'][$variation_data['variation']] = $variation_data;
          break;
        }
      }
    }

    $this->array_dataset->importDataSetAsArray($cached_records);
  }
}

?>
