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
require_once(dirname(__FILE__) . '/MediaObject.class.php');

class FileObject extends MediaObject
{
  public function create($is_root = false)
  {
    $this->_createFile();

    return parent :: create($is_root);
  }

  public function update($force_create_new_version = true)
  {
    $this->_updateFile();

    parent :: update($force_create_new_version);
  }

  protected function _createFile()
  {
    $tmp_file_path = $this->get('tmp_file_path');
    $file_name = $this->get('file_name');
    $mime_type = $this->get('mime_type');

    $media_id = $this->_createMediaRecord($tmp_file_path, $file_name, $mime_type);

    $this->set('media_id', $media_id);
  }

  protected function _updateFile()
  {
    $tmp_file_path = $this->get('tmp_file_path');
    $file_name = $this->get('file_name');
    $mime_type = $this->get('mime_type');

    if(!$media_id = $this->get('media_id'))
      throw new LimbException('media id not set');

    $this->_updateMediaRecord($media_id, $tmp_file_path, $file_name, $mime_type);
  }

  public function fetch($params=array(), $sql_params=array())
  {
    $sql_params['columns'][] = ' m.file_name as file_name, m.mime_type as mime_type, m.etag as etag, m.size as size, ';
    $sql_params['tables'][] = ', media as m ';
    $sql_params['conditions'][] = ' AND tn.media_id=m.id ';

    $records = parent :: fetch($params, $sql_params);

    return $records;
  }

}

?>
