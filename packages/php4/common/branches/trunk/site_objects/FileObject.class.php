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
require_once(LIMB_DIR . '/class/DomainObject.class.php');

class FileObject extends DomainObject
{
  var $_media_manager;

  function _getMediaManager()
  {
    if($this->_media_manager)
      return $this->_media_manager;

    include_once(dirname(__FILE__) . '/MediaManager.class.php');
    $this->_media_manager = new MediaManager();

    return $this->_media_manager;
  }

  function getMediaFile()
  {
    $mgr =& $this->_getMediaManager();
    return $mgr->getMediaFilePath($this->getMediaFileId());
  }

  function loadFromFile($file)
  {
    $mgr =& $this->_getMediaManager();
    $media_file_id = $mgr->store($file);
    $this->setMediaFileId($media_file_id);
  }

  function getEtag()
  {
    return $this->get('etag');
  }

  function setEtag($etag)
  {
    $this->set('etag', $etag);
  }

  function getName()
  {
    return $this->get('name');
  }

  function setName($name)
  {
    $this->set('name', $name);
  }

  function getFileName()
  {
    return $this->get('file_name');
  }

  function setFileName($file_name)
  {
    $this->set('file_name', $file_name);
  }

  function getMimeType()
  {
    return $this->get('mime_type');
  }

  function setMimeType($mime_type)
  {
    $this->set('mime_type', $mime_type);
  }

  function getMediaFileId()
  {
    return $this->get('media_file_id');
  }

  function setMediaFileId($media_file_id)
  {
    $this->set('media_file_id', $media_file_id);
  }

  function getMediaId()
  {
    return (int)$this->get('media_id');
  }

  function setMediaId($media_id)
  {
    $this->set('media_id', (int)$media_id);
  }
}

?>
