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


class user_tag_info
{
	var $tag = 'user';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'user_tag';
} 

register_tag(new user_tag_info());

/**
* The parent compile time component for lists
*/
class user_tag extends compiler_directive_tag
{
	function generate_contents(&$code)
	{		
		$logged_in_child =& $this->find_child('logged_in');
		$not_logged_in_child =& $this->find_child('not_logged_in');
		
		$in_groups =& $this->find_child('in_groups');
		$not_in_groups =& $this->find_child('not_in_groups');

		if ($logged_in_child)
		{
			$code->write_php('if (user :: is_logged_in()) {');
				$logged_in_child->generate($code);
			$code->write_php("}");
		}

		if ($not_logged_in_child)		
		{
			$code->write_php('if (! user :: is_logged_in()) {');
				$not_logged_in_child->generate($code);
			$code->write_php("}");
		}
		

		if ($in_groups)
		{
			if($groups = $this->_get_groups_list($in_groups))
			{
				$code->write_php("if (user :: is_logged_in() && (user :: is_in_groups({$groups}))) {");
					$in_groups->generate($code);
				$code->write_php("}");
			}
		}

		if ($not_in_groups)
		{
			if($groups = $this->_get_groups_list($not_in_groups))
			{
				$code->write_php("if (user :: is_logged_in() && (!user :: is_in_groups({$groups}))) {");
					$not_in_groups->generate($code);
				$code->write_php("}");
			}
		}
	} 
	
	function _get_groups_list( &$compiler_component)
	{
		if(!isset($compiler_component->attributes['groups']))
			return false;
		
		$groups = explode(',', $compiler_component->attributes['groups']);
		if(!is_array($groups))
			return false;
		
		$result = array();
		foreach($groups as $group)
		{
			$group = trim($group);
			$result[] = "'{$group}' => '{$group}'";;
		}	
		$result = implode(',', $result);
		$result = 'array('. $result .')';
		return $result;
	}
} 

?>