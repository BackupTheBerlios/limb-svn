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

	function init_richedit()
	{
		$this->_load_js_script();
		
		$id = $this->get_attribute('id');
		
		if ($this->get_attribute('mode') == 'light')
		  $init_function = 'install_limb_lite_extension(editor.config)';
		else
		  $init_function = 'install_limb_full_extension(editor.config)';
		
    echo "
    <script type='text/javascript'>
    
      function init_richedit_{$id}()
      {
	    	var editor = new HTMLArea('{$id}');
	    	
	    	{$init_function}
	    	
        editor.config.width = '600px';
        editor.config.height = '400px';  	    	
        editor.generate();
      }
      add_event(window, 'load', init_richedit_{$id});
    </script>";
	}
} 

?>