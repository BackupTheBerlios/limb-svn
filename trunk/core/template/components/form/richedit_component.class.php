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
require_once(LIMB_DIR . 'core/template/components/form/text_area_component.class.php');

class richedit_component extends text_area_component
{
	function render_contents()
	{
		echo htmlspecialchars($this->get_value(), ENT_QUOTES);
	} 
	
	function init_richedit()
	{
		if ($this->get_attribute('mode') === 'light')
			$this->load_light_richedit();
		else
			$this->load_richedit();
	}

	function _load_js_script()
	{
		if (defined('HTMLAREA_SCRIPT_LOADED'))
			return;
			
		define('HTMLAREA_SCRIPT_LOADED', 1);

		echo "
    <script type='text/javascript'>  
      var _editor_url = '/shared/HTMLArea-3.0-rc1/';    
    </script>
  	
    <script type='text/javascript' src='/shared/HTMLArea-3.0-rc1/htmlarea.js'></script>
    <script type='text/javascript' src='/shared/js/htmlarea_extension.js'></script>";	
	}

	function load_richedit()
	{
		$this->_load_js_script();
		
		$id = $this->get_attribute('id');
		
    echo "
    <script type='text/javascript'>
    
      function init_richedit_{$id}()
      {
	    	var editor = new HTMLArea('{$id}');
	    	
	    	install_limb_full_extension(editor.config);
	    	
        editor.config.width = '600px';
        editor.config.height = '400px';  	    	
        editor.generate();
      }
      add_event(window, 'load', init_richedit_{$id});
    </script>";
	}

	function load_light_richedit()
	{
		$this->_load_js_script();

		$id = $this->get_attribute('id');
		
    echo "
    <script type='text/javascript'>
      function init_light_richedit_{$id}()
      {
	    	var editor = new HTMLArea('{$id}');
	    	
	    	editor.config.toolbar = [
					[ 'bold', 'italic', 'underline', 'strikethrough', 'separator',
					  'subscript', 'superscript', 'separator',
					  'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', 'separator',
						'insertorderedlist', 'insertunorderedlist', 'outdent', 'indent', 'separator',
						'copy', 'cut', 'paste','separator',
					  'inserthorizontalrule', 'createlink', 'htmlmode', 'separator',
					  'popupeditor', 'separator'
					]
				];

        editor.config.width = '600px';
        editor.config.height = '400px';  
        editor.generate();
      }
      add_event(window, 'load', init_light_richedit_{$id});
    </script>";
	}
} 

?>