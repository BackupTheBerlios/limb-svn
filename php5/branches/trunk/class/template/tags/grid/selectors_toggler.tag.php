<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
class grid_selectors_toggler_tag_info
{
	public $tag = 'grid:SELECTORS_TOGGLER';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'grid_selectors_toggler_tag';
} 

register_tag(new grid_selectors_toggler_tag_info());

class grid_selectors_toggler_tag extends compiler_directive_tag
{
	public function check_nesting_level()
	{
		if (!$this->find_parent_by_class('grid_list_tag'))
		{
			throw new WactException('missing enclosure', 
					array('tag' => $this->tag,
					'enclosing_tag' => 'grid:LIST',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	}
	
	public function generate_contents($code)
	{
  	$md5id = substr(md5($this->get_server_id()), 0, 5);
  	
  	if(isset($this->attributes['form_id']))
  		$form_id = $this->attributes['form_id'];
  	else
  	{
  		$grid = $this->find_parent_by_class('grid_list_tag');
  		$form_id = 'grid_form_' . $grid->get_server_id();
  	}
  	
  	if(isset($this->attributes['selector_name']))
  		$selector_name = $this->attributes['selector_name'];
  	else
  		$selector_name = 'ids';

  	if(isset($this->attributes['img_src']))
  		$img_src = $this->attributes['img_src'];
  	else
  		$img_src = '/shared/images/selected.gif';
  		
		$js = "
		<script language='javascript'>		
		window.toggle_mark_{$md5id} = 0;
		function selectors_toggle_{$md5id}()
		{ 
			if(window.toggle_mark_{$md5id} == 0)
				window.toggle_mark_{$md5id} = 1;
			else
				window.toggle_mark_{$md5id} = 0;
			
			form = document.getElementById('{$form_id}');

			if(!form)
				return;
			
		  for (i = 0; i < form.elements.length; i++)
	    {
	     var item = form.elements[i];
	     
	     if (item.name.indexOf(form.name + '[{$selector_name}]') != -1)
	      item.checked = toggle_mark_{$md5id};
	    }
		}
		</script>";
		
		$code->write_html($js);
		
		$code->write_html("<img src='{$img_src}' onclick='selectors_toggle_{$md5id}()'>");

	} 
} 

?>