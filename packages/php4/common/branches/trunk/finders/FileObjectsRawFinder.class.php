
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

class FileObjectsRawFinder extends OneTableObjectsRawFinder
{
  function _defineDbTableName()
  {
    return 'file_object';
  }

  //for mocking
  function _doParentFind($params, $sql_params)
  {
    return parent :: find($params, $sql_params);
  }

  function find($params=array(), $sql_params=array())
  {
    $sql_params['columns'][] = ' m.file_name as file_name, m.mime_type as mime_type, m.etag as etag, m.size as size, ';
    $sql_params['tables'][] = ', media as m ';
    $sql_params['conditions'][] = ' AND tn.media_id=m.id ';

    if(!$records = $this->_doParentFind($params, $sql_params))
      return array();

    return $records;
  }
}
?>

