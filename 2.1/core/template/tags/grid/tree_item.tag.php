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


define('TREE_LINE_IMG', "<table border=0 cellspacing=0 cellpadding=0 height=100% style='display:inline'><tr><td background='/shared/images/t_l.gif' width=20><img src='/shared/images/1x1.gif'></td></tr></table>");
define('TREE_SPACER_IMG', "<img src='/shared/images/0.gif' width=20>");
define('TREE_END_IMG', 	"<table border=0 cellspacing=0 cellpadding=0 height=100% style='display:inline'><tr><td valign=top><img src='/shared/images/t_e.gif'></td></tr></table>");
define('TREE_END_P_IMG', "<table border=0 cellspacing=0 cellpadding=0 height=100%% style='display:inline'><tr><td><a href='%s'><img src='/shared/images/t_e_p.gif'></a></td></tr><tr><td height=100%%></td></tr></table>");
define('TREE_END_M_IMG', "<table border=0 cellspacing=0 cellpadding=0 height=100%% style='display:inline'><tr><td><a href='%s'><img src='/shared/images/t_e_m.gif' border=0></a></td></tr><tr><td height=100%%></td></tr></table>");
define('TREE_CROSS_IMG', "<table border=0 cellspacing=0 cellpadding=0 height=100%% style='display:inline'><tr><td background='/shared/images/t_l.gif' valign=top><img src='/shared/images/t_c.gif' border=0></td></tr></table>");
define('TREE_CROSS_P_IMG', "<table border=0 cellspacing=0 cellpadding=0 height=100%% style='display:inline'><tr><td background='/shared/images/t_l.gif' valign=top><a href='%s'><img src='/shared/images/t_c_p.gif' border=0></a></td></tr></table>");
define('TREE_CROSS_M_IMG', "<table border=0 cellspacing=0 cellpadding=0 height=100%% style='display:inline'><tr><td background='/shared/images/t_l.gif' valign=top><a href='%s'><img src='/shared/images/t_c_m.gif' border=0></a></td></tr></table>");

class grid_tree_item_tag_info
{
	var $tag = 'grid:TREE_ITEM';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'grid_tree_item_tag';
} 

register_tag(new grid_tree_item_tag_info());

class grid_tree_item_tag extends compiler_directive_tag 
{
	function check_nesting_level()
	{
		if (!is_a($this->parent, 'grid_iterator_tag'))
		{
			error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'enclosing_tag' => 'grid:ITERATOR',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 

	function generate_contents(&$code)
	{
		$ref = $this->get_component_ref_code();
			
		$code->write_html("<table border='0' cellpadding='0' cellspacing='0' height='100%'><tr><td>");
		
		$i = '$' . $code->get_temp_variable();
		$node_htm = '$' . $code->get_temp_variable();
		$level = '$' . $code->get_temp_variable();
		$levels_status = '$' . $code->get_temp_variable();

		$code->write_php(
			"{$node_htm} = '';
			{$level} = {$ref}->get('level');
			{$levels_status} = {$ref}->get('levels_status');
			"
		);

		$code->write_php("
			for({$i}=1; {$i} < {$level}; {$i}++)
			{
				if(isset({$levels_status}[{$i}]) && {$levels_status}[{$i}])
					{$node_htm} .= \"" . TREE_SPACER_IMG . "\";
				else
					{$node_htm} .= \"" . TREE_LINE_IMG . "\";
			}
		");
			
		$open_params = '$' . $code->get_temp_variable();
		$close_params = '$' . $code->get_temp_variable();
		$open_link = '$' . $code->get_temp_variable();
		$close_link = '$' . $code->get_temp_variable();
		$anchor = '$' . $code->get_temp_variable();
		$next_img = '$' . $code->get_temp_variable();
		$tmp = '$' . $code->get_temp_variable();

		$code->write_php("
			{$open_params}['id'] = {$ref}->get('node_id'); 
			{$open_params}['action'] = 'toggle';{$open_params}['expand'] = 1;
			{$close_params}['id'] = {$ref}->get('node_id');
			{$close_params}['action'] = 'toggle';{$close_params}['collapse'] = 1;
			{$anchor} = '#' . {$ref}->get('node_id');
			"
		);
			
		$code->write_php("
			if({$ref}->get('is_last_child'))
			{
				{$open_link} = sprintf(\"" . TREE_END_P_IMG . "\", add_url_query_items(PHP_SELF, {$open_params}) . {$anchor});
				{$close_link} = sprintf(\"" . TREE_END_M_IMG . "\", add_url_query_items(PHP_SELF, {$close_params}) . {$anchor});
				{$next_img} = \"" . TREE_END_IMG . "\";
			}
			else
			{
				{$open_link} = sprintf(\"" . TREE_CROSS_P_IMG . "\", add_url_query_items(PHP_SELF, {$open_params}) . {$anchor});
				{$close_link} = sprintf(\"" . TREE_CROSS_M_IMG . "\", add_url_query_items(PHP_SELF, {$close_params}) . {$anchor});
				{$next_img} = \"" . TREE_CROSS_IMG . "\";
			}
		");

		$code->write_php("
			if({$ref}->get('can_be_parent'))
			{
				if({$ref}->get('is_expanded'))
					{$node_htm} .= {$close_link};
				else
					{$node_htm} .= {$open_link};
			}
			else
					{$node_htm} .= {$next_img};
		");
	
		$code->write_php("echo '<a name=' . {$ref}->get('node_id') . '>';");
		
		$code->write_php("echo {$node_htm};");
			
		$code->write_html("</td><td nowrap class='text'>");
		
		$img_alt = '$' . $code->get_temp_variable();
		$img_htm = '$' . $code->get_temp_variable();
		
		$code->write_php("
			if(!{$ref}->get('img_alt'))
				{$img_alt} = {$ref}->get('identifier');
		");
				
		$code->write_php("echo 
			\"<table border=0 cellspacing=0 cellpadding=0 height=100% style='display:inline'>
				<tr>
					<td><img src='/shared/images/1x1.gif' height=3 width=1></td>
				</tr>
				<tr>
				<td>\";
		");
			
		$code->write_php("echo 
			\"<img alt='{$img_alt}' border='0' align='middle' src='\" . {$ref}->get('icon') . \"'>\";
		");
		
		$code->write_php("echo 
			\"</td></tr>\";
		");
		$code->write_php("
			echo \"<tr><td height=100% \";
			
			if({$ref}->get('can_be_parent'))
			{
				if({$ref}->get('is_expanded'))
				{
					echo \" background='/shared/images/t_l.gif'\";
				}
			}
				
			echo \"></td></tr>\";
		");
		$code->write_php("echo \"</table>\";");
		$code->write_html("</td><td valign=top style='padding:6px 3px 3px 2px'>");
		
		if(!array_key_exists('nolink', $this->attributes))
			$code->write_php("echo '<a href=' . {$ref}->get('path') . '>';");
		
		$code->write_php("echo {$ref}->get('identifier');");
		
		if(!array_key_exists('nolink', $this->attributes))
			$code->write_php("echo '</a>';");
		
		$code->write_html("</td></tr></table>");
				
		parent :: generate_contents($code);
	} 
} 

?>