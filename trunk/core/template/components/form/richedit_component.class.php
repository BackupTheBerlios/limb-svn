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
		if (defined('RICHEDIT_LOAD_SCRIPT'))
			return;
			
		define('RICHEDIT_LOAD_SCRIPT', 1);
    $light_script = "";
    
    $html_area_config = "
    	editor = new HTMLArea('" . $this->get_attribute('id') . "',HTMLArea.Config());
		  editor.registerPlugin('TableOperations');
			  // register the TableOperations plugin with our editor    	
    ";
    
    if ($this->get_attribute('mode') === 'light')
    {
    	$light_script = "<script type='text/javascript' src='/shared/richedit/light_richedit.js'></script> ";  
    	
    	$html_area_config = "light_editor = new HTMLAreaLight.Config(); editor = new HTMLArea('" . $this->get_attribute('id') . "',light_editor)";
    }
		
		echo "
    <script type='text/javascript' src='/shared/richedit/htmlarea.js'></script>
		{$light_script}
		<!-- load the TableOperations plugin files -->
		<script type='text/javascript' src='/shared/richedit/plugins/TableOperations/table-operations.js'></script>
		<script type='text/javascript' src='/shared/richedit/plugins/TableOperations/lang/en.js'></script>
		
		<script type='text/javascript' src='/shared/richedit/plugins/TableOperations/lang/en.js'></script>
		<script type='text/javascript' src='/shared/richedit/popupwin.js'></script>


    <script type='text/javascript' src='/shared/richedit/lang/en.js'></script>
    <script type='text/javascript' src='/shared/richedit/dialog.js'></script>
    <script type='text/javascript' src='/shared/richedit/popupurl.js'></script>
    <style type='text/css'>
      @import url(/shared/richedit/htmlarea.css);
    </style>
    <script type='text/javascript'>
      var editor = null;
    
      function init_richedit()
      {
       {$html_area_config}
        editor.config.editorURL = '/shared/richedit/';
        editor.generate();
      }
      add_event(window, 'load', init_richedit);
    </script>";

	}
} 

?>