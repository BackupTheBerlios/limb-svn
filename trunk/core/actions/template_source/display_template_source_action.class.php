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
require_once(LIMB_DIR . 'core/actions/action.class.php');
require_once(LIMB_DIR . 'core/template/fileschemes/simpleroot/compiler_support.inc.php');
require_once(LIMB_DIR . 'core/template/template_highlight_handler.class.php');
require_once(LIMB_DIR . '/core/lib/external/XML_HTMLSax/XML_HTMLSax.php');

define('TEMPLATE_FOR_HACKERS', '/template_source/for-hackers.html');

class display_template_source_action extends action
{
	var $history = array();
	
	function perform(&$request, &$response)
	{
		if(($t = $request->get_attribute('t')) && is_array($t) && sizeof($t) > 0)
		{
			$this->history = $t;
			$template_path = end($this->history);
		}
		else
			$template_path = TEMPLATE_FOR_HACKERS;
		
		if(substr($template_path, -5,  5) != '.html')
			$template_path = TEMPLATE_FOR_HACKERS;
		
		if(substr($template_path, -5,  5) != '.html')
		  $request->set_status(REQUEST_STATUS_FAILURE);
				
		if(!$source_file_path = resolve_template_source_file_name($template_path))
		{
			debug :: write_error('template not found',
	 			__FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
				array('template_path' => $this->template_path));
			
			$source_file_path = resolve_template_source_file_name(TEMPLATE_FOR_HACKERS);
		}
		
		$template_contents = file_get_contents($source_file_path);

		if(sizeof($this->history) > 1)
		{
			$tmp_history = $this->history;
			
			$from_template_path = $tmp_history[sizeof($tmp_history) - 2];
			$tmp_history = array_splice($tmp_history, 0, sizeof($tmp_history) - 1);
			
			$history_query = 't[]=' . implode('&t[]=', $tmp_history);
			
			$this->view->set('history_query', $history_query);
			$this->view->set('from_template_path', $from_template_path);
		}
		
		$this->view->set('template_path', $template_path);
		$this->view->set('template_content', $this->_process_template_content($template_contents));
	}
	
	function _get_template_path_from_node($node_id)
	{
		if(!$site_object =& wrap_with_site_object(fetch_one_by_node_id($node_id)))
			return null;
			
		$controller =& $site_object->get_controller();
		
		return $controller->get_action_property($controller->get_default_action(), 'template_path');
	}
	
	function _process_template_content($template_contents)
	{		
		global $tag_dictionary; //fixx
		
  	$parser =& new XML_HTMLSax();
  	
  	$handler =& new template_highlight_handler($tag_dictionary);
  	
  	$handler->set_template_path_history($this->history);
  	
  	$parser->set_object($handler);
  	
		$parser->set_element_handler('open_handler','close_handler');
		$parser->set_data_handler('data_handler');
		$parser->set_escape_handler('escape_handler');

		$parser->parse($template_contents);
		
		$html = $handler->get_html();
		
		return $html;
	}
}

?>