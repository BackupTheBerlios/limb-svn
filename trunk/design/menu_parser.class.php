<?php
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
	}
?>