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

require_once(LIMB_DIR . 'core/template/components/form/input_form_element.class.php');

class image_select_component extends input_form_element
{
	function init_image_select()
	{
		if (!defined('IMAGE_SELECT_LOAD_SCRIPT'))
		{
			echo "<script type='text/javascript' src='/shared/js/image_select.js'></script>";
			define('IMAGE_SELECT_LOAD_SCRIPT',1);
		}	
	}
	
	function render_image_select()
	{ 
		$id = $this->get_attribute('id');	
  	$md5id = substr(md5($id), 0, 5);

  	$image_node_id = $this->get_value();

  	$start_path = '';

  	if($image_node_id && $image_data = fetch_one_by_node_id($image_node_id))
  	{
			$span_name = $image_data['identifier'];
			$start_path = '/root?action=image_select&node_id=' . $image_data['parent_node_id'];
		}
		else
			$span_name = '';

  	if(!$start_path)
  	{
	 		$start_path = $this->get_attribute('start_path');
	  	if(!$start_path)
	  		$start_path = session :: get('limb_image_select_working_path');
	  	if(!$start_path)
				$start_path = '/root/images_folder';

			$start_path .= '?action=image_select';
		}

  	echo "<span id='{$md5id}_name'>{$span_name}</span><br><img id='{$md5id}_img' src='/shared/images/1x1.gif'/>
	    <script type='text/javascript'>
	    	var image_select_{$md5id};
	    	
	      function init_image_select_{$md5id}()
	      {
	        image_select_{$md5id} = new image_select('{$id}', '{$md5id}');
	        image_select_{$md5id}.set_start_path('{$start_path}');
	        image_select_{$md5id}.generate();
	      }
	      
	      function image_select_{$md5id}_insert_image(image)
	      {
	      	image_select_{$md5id}.insert_image(image);
	      }

	      function image_select_{$md5id}_get_image()
	      {	        
	      	return image_select_{$md5id}.get_image();
	      }
	      
	      function image_reset_{$md5id}()
	      {
	      	image_select_{$md5id}.id_container.value = 0;
	      	init_image_select_{$md5id}();
	      }
	     
	      add_event(window, 'load', init_image_select_{$md5id});
	    </script>";
	    
	  echo "<br><br><input class='button' type='button' onclick='popup(\"/root/image_select?properties=0\", null, null, false, image_select_{$md5id}_insert_image, image_select_{$md5id}_get_image)' value='" . strings :: get('select_image', 'image') . "'>";
	  echo '&nbsp;';
	  echo "<input class='button' type='button' onclick='image_reset_{$md5id}()' value='" . strings :: get('reset') . "'>";
	}

} 
?>