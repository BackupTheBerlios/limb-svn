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
  var $html;
  var $current_tag;

  function template_highlight_handler()
  {
  	$this->html = '';
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
						$value = "<a href=/root/template_source?template_path={$value}>$value</a>";
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
  	return $this->html;
  }
}
?>