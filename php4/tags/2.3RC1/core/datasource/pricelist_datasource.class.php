<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/core/datasource/fetch_sub_branch_datasource.class.php');
require_once(LIMB_DIR . '/core/lib/util/mime_type.class.php');

class pricelist_datasource extends fetch_sub_branch_datasource
{
	function & get_dataset(& $counter, $params = array())
	{
		$mime_type = new mime_type();

		$pricelists_array =& $this->_fetch($counter, $params);
				
		$file_ids = array();
		foreach($pricelists_array as $id => $data)
			if($data['file_id'] > 0)
				$file_ids[$data['file_id']] = $data['file_id'];
		
		if(!$file_ids)
			return new array_dataset($pricelists_array);

		$files_counter = null;
		$file_data = fetch_by_node_ids($file_ids, 'file_object', $files_counter);

		foreach($pricelists_array as $id => $data)
			if($data['file_id'] > 0)
			{
				$pricelists_array[$id]['file_size'] = $file_data[$data['file_id']]['size'];
				$pricelists_array[$id]['file_icon'] = $mime_type->get_type_icon($file_data[$data['file_id']]['mime_type']);
			}
		
		
		return new array_dataset($pricelists_array);
	}
	
}


?>