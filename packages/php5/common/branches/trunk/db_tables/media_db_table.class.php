<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/db/db_table.class.php');

class media_db_table extends db_table
{
  protected function _define_columns()
  {
  	return array(
      'id' => '',
      'file_name' => '',
      'mime_type' => '',
      'size' => array('type' => 'numeric'),
      'etag' => '',
    );
  }  
  
  protected function _delete_media_files($ids)
  {
		foreach($ids as $id)
			unlink(MEDIA_DIR . $id . '.media');
  }
  	
	protected function _delete_operation($conditions, $affected_rows)
	{
		parent :: _delete_operation($conditions, $affected_rows);
		
		$this->_delete_media_files(complex_array :: get_column_values('id', $affected_rows));
	}	
}

?>