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

define('RICHEDIT_DEFAULT_WIDTH', '600px');
define('RICHEDIT_DEFAULT_HEIGHT', '400px');
define('RICHEDIT_DEFAULT_ROWS', '30');
define('RICHEDIT_DEFAULT_COLS', '60');

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
      var _editor_lang = 'en'; 
    </script>
  	
    <script type='text/javascript' src='/shared/HTMLArea-3.0-rc1/htmlarea.js'></script>
    <script type='text/javascript'>  
      HTMLArea.loadPlugin('TableOperations');
    </script>
    <script type='text/javascript' src='/shared/js/htmlarea_extension.js'></script>
    
		<script type='text/javascript'>
		  function toggle_richedit_textarea()
		  {
		    c = get_cookie('use_textarea_instead_of_richedit');
		    
		    if(c == 1)
		      remove_cookie('use_textarea_instead_of_richedit');
		    else
		      set_cookie('use_textarea_instead_of_richedit', 1);
		  }
		</script>";
	}

	function init_richedit()
	{
		$this->_load_js_script();
				
		echo '<button class="button" onclick="toggle_richedit_textarea();window.location.reload();return false;">
		      Toggle richedit/textarea
		      </button><br>';
		
		
		$id = $this->get_attribute('id');
		
		if ($this->get_attribute('mode') == 'light')
		  $init_function = 'install_limb_lite_extension(editor.config);';
		else
		  $init_function = 'install_limb_full_extension(editor.config);editor.registerPlugin(TableOperations);';
		  
		if(!$this->get_attribute('rows'))
		  $this->set_attribute('rows', RICHEDIT_DEFAULT_ROWS);

		if(!$this->get_attribute('cols'))
		  $this->set_attribute('cols', RICHEDIT_DEFAULT_COLS);

		if(!$width = $this->get_attribute('width'))
		  $width = RICHEDIT_DEFAULT_WIDTH;

		if(!$height = $this->get_attribute('height'))
		  $height = RICHEDIT_DEFAULT_HEIGHT;
		
    echo "
    <script type='text/javascript'>
    
      function init_richedit_{$id}()
      {
	    	var editor = new HTMLArea('{$id}');
	    	
	    	{$init_function}
	    	
        editor.config.width = '{$width}';
        editor.config.height = '{$height}';  	    	
        editor.generate();
      }
      
      c = get_cookie('use_textarea_instead_of_richedit');
      if(c != 1)
        add_event(window, 'load', init_richedit_{$id});      
      
    </script>";
	}
} 

?>