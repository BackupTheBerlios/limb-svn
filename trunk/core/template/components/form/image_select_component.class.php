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
		if (defined('IMAGE_SELECT_LOAD_SCRIPT'))
			return;
					
		echo "<script type='text/javascript' src='/shared/js/image_select.js'></script>";
		
		if (!defined('RICHEDIT_POPURL_SCRIPT'))
    	echo "<script type='text/javascript' src='/shared/richedit/popupurl.js'></script>";
			
		define('IMAGE_SELECT_LOAD_SCRIPT',1);
		define('RICHEDIT_POPURL_SCRIPT',1);
	}
	
	function render_image_select()
	{ 
		$id = $this->get_attribute('id'); 	  	
  	$md5id = substr(md5($id), 0, 5);
  	  	
  	echo "<img id='{$md5id}_img' src='/shared/images/1x1.gif'/>
	    <script type='text/javascript'>
	    	var image_select_{$md5id};
	    	
	      function init_image_select_{$md5id}()
	      {
	        image_select_{$md5id} = new image_select('{$id}', '{$md5id}_img');
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
	    
	  echo "<input class='button' type='button' onclick='PopupURL(null, \"/root/image_select?properties=0\", image_select_{$md5id}_insert_image, image_select_{$md5id}_get_image)' value='Select image'>";
	  echo '&nbsp;';
	  echo "<input class='button' type='button' onclick='image_reset_{$md5id}()' value='Reset'>";
	}

} 
?>