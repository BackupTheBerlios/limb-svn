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
require_once(LIMB_DIR . '/core/data_mappers/OneTableObjectsMapper.class.php');

class ImageObjectMapper extends OneTableObjectsMapper
{
  function _getFinder()
  {
    include_once(dirname(__FILE__) . '/../finders/ImageObjectsRawFinder.class.php');
    return new ImageObjectsRawFinder();
  }

  function _createDomainObject()
  {
    include_once(dirname(__FILE__) . '/../site_objects/ImageObject.class.php');
    return new ImageObject();
  }

  function _defineDbTableName()
  {
    return 'image_object';
  }

  function _doLoad($result_set, $domain_object)
  {
    $variations_data = $result_set['variations'];
    unset($result_set['variations']);

    $domain_object->import($result_set);

    $this->_attachVariations($variations_data, $domain_object);
  }

  function _attachVariations($variations_data, $domain_object)
  {
    include_once(dirname(__FILE__) . '/../ImageVariation.class.php');

    foreach($variations_data as $key => $data)
    {
      $variation = new ImageVariation();
      $variation->import($data);
      $domain_object->attachVariation($variation);
    }
  }

  function insert($domain_object)
  {
    $this->_doParentInsert($domain_object);

    $this->_insertVariations($domain_object);
  }

  function update($domain_object)
  {
    $this->_doParentUpdate($domain_object);

    $this->_updateVariations($domain_object);
  }

  function _updateVariations($domain_object)
  {
    $variations = $domain_object->getVariations();

    $toolkit =& Limb :: toolkit();
    $media_db_table =& $toolkit->createDBTable('Media');
    $variation_db_table =& $toolkit->createDBTable('ImageVariation');

    foreach($variations as $variation)
    {
      if($variation->getId())
      {
        $this->_updateVariation($domain_object, $variation);
      }
      else
      {
        $this->_insertVariation($domain_object, $variation);
      }
    }
  }

  function _updateVariation($domain_object, $variation)
  {
    $media_db_table = Limb :: toolkit()->createDBTable('Media');
    $variation_db_table = Limb :: toolkit()->createDBTable('ImageVariation');

    $old_media = $media_db_table->getRowById($variation->getMediaId());

    if($old_media['media_file_id'] != $variation->getMediaFileId())
    {
      $mgr =& $this->_getMediaManager()
      $mgr->unlinkMedia($old_media['media_file_id']);
    }

    $media_record = array('file_name' => $variation->getFileName(),
                          'mime_type' => $variation->getMimeType(),
                          'size' => $variation->getSize(),
                          'etag' => $variation->getEtag(),
                          'media_file_id' => $variation->getMediaFileId());

    $media_db_table->updateById($variation->getMediaId(), $media_record);

    $image_variation_record = array('image_id' => $domain_object->getId(),
                                    'media_id' => $variation->getMediaId(),
                                    'width' => $variation->getWidth(),
                                    'height' => $variation->getHeight(),
                                    'variation' => $variation->getName());

    $variation_db_table->updateById($variation->getId(), $image_variation_record);
  }

  function _insertVariations($domain_object)
  {
    $variations = $domain_object->getVariations();

    foreach($variations as $variation)
    {
      $this->_insertVariation($domain_object, $variation);
    }
  }

  function _insertVariation($domain_object, $variation)
  {
    $toolkit =& Limb :: toolkit();
    $media_db_table =& $toolkit->createDBTable('Media');
    $variation_db_table =& $toolkit->createDBTable('ImageVariation');

    $media_record = array('media_file_id' => $variation->getMediaFileId(),
                          'file_name' => $variation->getFileName(),
                          'mime_type' => $variation->getMimeType(),
                          'size' => $variation->getSize(),
                          'etag' => $variation->getEtag());

    $media_db_table->insert($media_record);

    $variation->setMediaId($media_db_table->getLastInsertId());

    $image_variation_record = array('image_id' => $domain_object->getId(),
                          'media_id' => $variation->getMediaId(),
                          'width' => $variation->getWidth(),
                          'height' => $variation->getHeight(),
                          'variation' => $variation->getName());

    $variation_db_table->insert($image_variation_record);
  }

  function _getMediaManager()
  {
    include_once(dirname(__FILE__) . '/../MediaManager.class.php');
    return new MediaManager();
  }

}

?>
