<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: image_select_component.class.php 46 2004-03-19 12:45:55Z server $
*
***********************************************************************************/

require_once(LIMB_DIR . 'core/template/components/form/input_form_element.class.php');

class file_select_component extends input_form_element
{
	function init_file_select()
	{
		if (defined('FILE_SELECT_LOAD_SCRIPT'))
			return;
					
		echo "<script type='text/javascript' src='/shared/js/file_select.js'></script>";
		
		if (!defined('RICHEDIT_POPURL_SCRIPT'))
    	echo "<script type='text/javascript' src='/shared/richedit/popupurl.js'></script>";
			
		define('FILE_SELECT_LOAD_SCRIPT',1);
		define('RICHEDIT_POPURL_SCRIPT',1);
	}
	
	function render_file_select()
	{ 
		$id = $this->get_attribute('id');
  	$md5id = substr(md5($id), 0, 5);
  	
  	if($file_data = fetch_one_by_node_id($this->get_value()))
  	{
			$span_name = $file_data['identifier'];
			$span_description = $file_data['description'];
		}
		else
		{
			$span_name = '';
			$span_description = '';
		}

  	  	
  	echo "<img id='{$md5id}_img' src='/shared/images/1x1.gif'/><span id='{$md5id}_name'>{$span_name}</span><span id='{$md5id}_description'>{$span_description}</span>
	    <script type='text/javascript'>
	    	var file_select_{$md5id};
	    	
	      function init_file_select_{$md5id}()
	      {
	        file_select_{$md5id} = new file_select('{$id}', '{$md5id}_img', '{$md5id}_name', '{$md5id}_description');
	        file_select_{$md5id}.generate();
	      }
	      
	      function file_select_{$md5id}_insert_file(file)
	      {
	      	file_select_{$md5id}.insert_file(file);
	      }

	      function file_select_{$md5id}_get_file()
	      {
	      	return file_select_{$md5id}.get_file();
	      }
	      
	      function image_reset_{$md5id}()
	      {
	      	file_select_{$md5id}.id_container.value = 0;
	      	init_file_select_{$md5id}();
	      }
	     
	      add_event(window, 'load', init_file_select_{$md5id});
	    </script>";
	    
	  echo "<input class='button' type='button' onclick='PopupURL(null, \"/root/file_select?\", file_select_{$md5id}_insert_file, file_select_{$md5id}_get_file)' value='Select file'>";
	  echo '&nbsp;';
	  echo "<input class='button' type='button' onclick='file_reset_{$md5id}()' value='Reset'>";
	}
	
} 
?>