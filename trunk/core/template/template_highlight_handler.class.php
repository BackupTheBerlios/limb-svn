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
require_once(LIMB_DIR . '/core/lib/external/XML_HTMLSax/XML_HTMLSax.php');

class template_highlight_handler 
{
  var $html = '';
  var $current_tag = '';
  var $template_path_history = array();

  function template_highlight_handler()
  {
  }
  
  function set_template_path_history($history)
  {
  	$this->template_path_history = $history;
  }
	
	function write_attributes($attributes)
	{
		if (is_array($attributes)) 
		{
			foreach ($attributes as $name => $value) 
			{
				if($this->current_tag == 'core:wrap' || $this->current_tag == 'core:include')
				{
					if($name == 'file')
					{
						$history = array();
						$history = $this->template_path_history;
						$history[] = $value;
						
						$history_string = 't[]=' . implode('&t[]=', $history);
						
						$href = "/root/template_source?{$history_string}";
												
						$value = "<a style='text-decoration:underline;font-weight:bold;' href={$href}>{$value}</a>";
					}
				}
				
				$this->html .= ' ' . $name . '="' . $value . '"';
			}
		}
	}
	  
  function open_handler(& $parser, $name, $attrs) 
  {	
  	$this->current_tag = strtolower($name);
  	
    switch($name) 
    {
      default:
	      $this->html .= '&lt;<span style="color:blue">' . $name . '</span>';
	      $this->write_attributes($attrs);
	      $this->html .= '&gt;';
	    break;
    }
  }

  function close_handler(& $parser, $name) 
  {
  	$this->html .= '&lt;/<span style="color:blue">' . $name . '</span>&gt;';
  }

  function data_handler(& $parser, $data) 
  {
  	$this->html .= $data;
  }

  function escape_handler(& $parser, $data) 
  {
   	$this->html .= $data;
  }

  function get_html() 
  {
  	$this->html = preg_replace('~(\{(\$|\^|#)[^\}]+\})~', "<span style='background-color:lightgreen;font-weight:bold;'>\\1</span>", $this->html);
  	
  	return $this->html;
  }
}
?>