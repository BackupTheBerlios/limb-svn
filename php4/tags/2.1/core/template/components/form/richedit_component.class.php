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

	function _load_additional_js_scripts()
	{
		if (defined('RICHEDIT_LOAD_ADDITIONAL_SCRIPT'))
			return;
			
		define('RICHEDIT_LOAD_ADDITIONAL_SCRIPT', 1);

		echo "
		<!-- load the TableOperations plugin files -->
		<script type='text/javascript' src='/shared/richedit/plugins/TableOperations/table-operations.js'></script>
		<script type='text/javascript' src='/shared/richedit/plugins/TableOperations/lang/en.js'></script>
		";
	}

	function _load_basic_js_scripts()
	{
		if (defined('RICHEDIT_LOAD_BASIC_SCRIPT'))
			return;
			
		define('RICHEDIT_LOAD_BASIC_SCRIPT', 1);

		echo "
    <script type='text/javascript' src='/shared/richedit/htmlarea.js'></script>
		<script type='text/javascript' src='/shared/richedit/popupwin.js'></script>
    <script type='text/javascript' src='/shared/richedit/lang/en.js'></script>
    <script type='text/javascript' src='/shared/richedit/dialog.js'></script>
    <script type='text/javascript' src='/shared/richedit/popupurl.js'></script>
    <style type='text/css'>
      @import url(/shared/richedit/htmlarea.css);
    </style>";
	
	}

	function load_richedit()
	{
		$this->_load_basic_js_scripts();
		$this->_load_additional_js_scripts();
		$id = $this->get_attribute('id');
		
    echo "
    <script type='text/javascript'>
      var editor = null;
    
      function init_richedit()
      {
	    	editor = new HTMLArea('{$id}');
			  editor.registerPlugin('TableOperations');
        editor.config.editorURL = '/shared/richedit/';
        editor.generate();
      }
      add_event(window, 'load', init_richedit);
    </script>";
	}

	function load_light_richedit()
	{
		$this->_load_basic_js_scripts();

		$id = $this->get_attribute('id');
		
    echo "
    <script type='text/javascript'>
      var editor_light = null;
    
      function init_light_richedit()
      {
	    	editor_light = new HTMLArea('{$id}');
	    	
	    	editor_light.config.toolbar = [
					[ 'bold', 'italic', 'underline', 'strikethrough', 'separator',
					  'subscript', 'superscript', 'separator',
					  'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', 'separator',
						'insertorderedlist', 'insertunorderedlist', 'outdent', 'indent', 'separator',
						'copy', 'cut', 'paste','separator',
					  'inserthorizontalrule', 'createlink', 'insertimage', 'insertlinkfile', 'htmlmode', 'separator',
					  'popupeditor', 'separator', 'clear_msw'
					]
				];

        editor_light.config.editorURL = '/shared/richedit/';
        editor_light.generate();
      }
      add_event(window, 'load', init_light_richedit);
    </script>";

	}
} 

?>