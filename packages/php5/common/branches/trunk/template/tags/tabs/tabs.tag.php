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
class tabs_tag_info
{
	public $tag = 'tabs';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'tabs_tag';
} 

register_tag(new tabs_tag_info());

class tabs_tag extends compiler_directive_tag
{
  private $tabs = array();
	private $tabulator_class = 'class="tabulator"';
	private $tab_class = 'class="tab"';
	private $active_tab_class = 'class="active-tab"';

  public function prepare()
  {
	  if(isset($link->attributes['active_tab']))
	    $this->active_tab = $link->attributes['active_tab'];
	  else
	    $this->active_tab = null;
	
	  if(isset($link->attributes['class']))
	    $this->tabulator_class = 'class="' . $link->attributes['class'] . '"';

	  if(isset($link->attributes['tab_class']))
	    $this->tab_class = 'class="' . $link->attributes['tab_class'] . '"';

	  if(isset($link->attributes['active_tab_class']))
	    $this->active_tab_class = 'class="' . $link->attributes['active_tab_class'] . '"';
	    
    parent :: prepare();
  }
    	
	public function _load_tabs_js_script($code)
	{
		if (defined('TABS_SCRIPT_LOADED'))
			return;
			
		define('TABS_SCRIPT_LOADED', 1);

		$code->write_html("<script type='text/javascript' src='/shared/js/tabs.js'></script>");
	}	
	
	public function pre_generate($code)
	{	  	  
	  $this->_load_tabs_js_script($code);
	  
	  parent :: pre_generate($code);
	}
	
	public function post_generate($code)
	{
		$js = '';

	  if(isset($link->attributes['active_tab']))
	    $active_tab = $link->attributes['active_tab'];
	  else
	    $active_tab = reset($this->tabs);
	
	  if(!$this->tabs || !$active_tab || !in_array($active_tab, $this->tabs))
	  {
			error('INVALID_TABS_DECLARATION', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			array('tag' => $this->tag,
					'description' => 'check your tabs settings',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));	  
	  }
	  
	  foreach($this->tabs as $id)
	   $js .= "tabs.register_tab_item('{$id}');\n";
	   
	   
    $js .= "tabs.activate('{$active_tab}');\n";          
	
		$code->write_html("    	
      <script type='text/javascript'>
        var tabs = new tabs_container();
        {$js}
      </script>");
      
    parent :: post_generate($code);
	}
} 

?>