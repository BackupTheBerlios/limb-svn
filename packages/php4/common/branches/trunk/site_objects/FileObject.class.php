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
require_once(LIMB_DIR . '/class/core/DomainObject.class.php');

class FileObject extends DomainObject
{
  protected $_media_manager;

  protected function _getMediaManager()
  {
    if($this->_media_manager)
      return $this->_media_manager;

    include_once(dirname(__FILE__) . '/MediaManager.class.php');
    $this->_media_manager = new MediaManager();

    return $this->_media_manager;
  }

  public function getMediaFile()
  {
    return $this->_getMediaManager()->getMediaFilePath($this->getMediaFileId());
  }

  public function loadFromFile($file)
  {
    $media_file_id = $this->_getMediaManager()->store($file);
    $this->setMediaFileId($media_file_id);
  }

  public function getEtag()
  {
    return $this->get('etag');
  }

  public function setEtag($etag)
  {
    $this->set('etag', $etag);
  }

  public function getName()
  {
    return $this->get('name');
  }

  public function setName($name)
  {
    $this->set('name', $name);
  }

  public function getFileName()
  {
    return $this->get('file_name');
  }

  public function setFileName($file_name)
  {
    $this->set('file_name', $file_name);
  }

  public function getMimeType()
  {
    return $this->get('mime_type');
  }

  public function setMimeType($mime_type)
  {
    $this->set('mime_type', $mime_type);
  }

  public function getMediaFileId()
  {
    return $this->get('media_file_id');
  }

  public function setMediaFileId($media_file_id)
  {
    $this->set('media_file_id', $media_file_id);
  }

  public function getMediaId()
  {
    return (int)$this->get('media_id');
  }

  public function setMediaId($media_id)
  {
    $this->set('media_id', (int)$media_id);
  }
}

?>
