<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: poll_all_results_data_source.class.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/data_source/fetch_sub_branch_data_source.class.php');
require_once(LIMB_DIR . 'core/lib/util/mime_type.class.php');

class pricelist_data_source extends fetch_sub_branch_data_source
{
	function pricelist_data_source()
	{
		parent :: fetch_sub_branch_data_source();
	}

	function & get_data_set(& $counter, $params = array())
	{
		$mime_type = new mime_type();

		$pricelists_array =& $this->_fetch($counter, $params);
				
		$file_ids = array();
		foreach($pricelists_array as $id => $data)
			if($data['file_id'] > 0)
				$file_ids[$data['file_id']] = $data['file_id'];
		
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