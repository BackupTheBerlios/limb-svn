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
class grid_stripe_tag_info
{
	public $tag = 'grid:STRIPE';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'grid_stripe_tag';
} 

register_tag(new grid_stripe_tag_info());

class grid_stripe_tag extends compiler_directive_tag
{
	public function check_nesting_level()
	{
		if ($this->find_parent_by_class('grid_stripe_tag'))
		{
			throw new WactException('bad self nesting', 
					array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		
		if (!$this->parent instanceof grid_iterator_tag)
		{
			throw new WactException('missing enclosure', 
					array('tag' => $this->tag,
					'enclosing_tag' => 'grid:ITERATOR',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	}
	
	public function generate_contents($code)
	{	
		if (array_key_exists('even', $this->attributes))
		{
			$code->write_php('if (!(' . $this->get_dataspace_ref_code() . '->get_counter()%2)) {');
			parent :: generate_contents($code);
			$code->write_php('}');
		}
		elseif (array_key_exists('odd', $this->attributes))	
		{
			$code->write_php('if ((' . $this->get_dataspace_ref_code() . '->get_counter()%2)) {');
			parent :: generate_contents($code);
			$code->write_php('}');
		}
	} 
} 

?>