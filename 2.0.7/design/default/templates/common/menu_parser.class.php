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

	include_once(LIMB_DIR . 'core/lib/xml/sax_parser.class.php');
	
	class menu_parser extends sax_parser
	{
		function menu_parser()
		{
			parent :: sax_parser(null, 'func');
		}
		
		function parse()
		{
			if($phpbb_sid = session :: get('phpbb_sid'))
				$this->xml_string = str_replace('%phpbb_sid%', $phpbb_sid, $this->xml_string); 

			return parent :: parse();
		}
		
		function xmltag_navigation($xp, $elem, $attrs)
		{
			echo "<table width=100% border=0 cellspacing=2 cellpadding=0><tr><td>";
		}

		function xmltag_menu($xp, $elem, $attrs)
		{
			global $menu_index;
			
			if (!isset($menu_index)) 
				$menu_index = 0;
			
			if (empty($attrs['URL'])) 
				$url = "javascript:toggle_submenu({$menu_index})";
			else 
				$url = $attrs['URL'];
			
			$opened_submenu = isset($_COOKIE['opened_submenu']) ? $_COOKIE['opened_submenu'] : '';
			
			if (strlen($opened_submenu))
				$opened_submenu_array = explode(',', $opened_submenu);
			else
				$opened_submenu_array = array();
				
			if (is_array($opened_submenu_array) && in_array($menu_index, $opened_submenu_array))
			{
				$img = 'right_arrow';
				$display = 'block';
			}
			else
			{
				$img = 'down_arrow';
				$display = 'none';
			}
			echo "<table border=0 cellspacing=3 cellpadding=0>
					<tr>
						<td valign=top width=1><img id='submenu{$menu_index}' onclick='toggle_submenu({$menu_index})' src='/shared/images/{$img}.gif'></td>
						<td valign=top><a class=menu_links href='$url'>".$attrs["TITLE"]."</a><div id=sb{$menu_index} style='display:{$display};'>";
			$menu_index++;
		}

		function xmltag_item($xp, $elem, $attrs)
		{
			if(isset($attrs['IS_SELECTED']))
				$css = 'menu_selected_links';
			else
				$css = 'menu_links';
				
			$a = "<a class='$css' href='".$attrs['URL']."'>".$attrs['TITLE']."</a>";
							
			echo "<table border=0 cellspacing=3 cellpadding=0><tr><td valign=top width=1><img src='/shared/images/bullet1.gif'></td><td>$a";
		}
		
		function xmltag_navigation_($xp, $elem)
		{
			echo "</td></tr></table>";
		}

		function xmltag_menu_($xp, $elem)
		{
			echo "</div></td></tr></table>";
		}

		function xmltag_item_($xp, $elem)
		{
			echo "</td></tr></table>";
		}
	}
?>