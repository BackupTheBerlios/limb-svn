<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: tree_toggle_action.class.php 28 2004-03-10 16:03:19Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/http/http_request.inc.php');
require_once(LIMB_DIR . 'core/actions/action.class.php');
require_once(LIMB_DIR . 'core/template/fileschemes/simpleroot/compiler_support.inc.php');
require_once(LIMB_DIR . 'core/template/template_highlight_handler.class.php');
require_once(LIMB_DIR . '/core/lib/external/XML_HTMLSax/XML_HTMLSax.php');

class display_template_source_action extends action
{
	function display_template_source_action($name='')
	{
		parent :: action($name);
	}
	
	function perform()
	{
		if(isset($_REQUEST['template_node_id']))
		{
			$template_node_id = (int)$_REQUEST['template_node_id'];
			if(!$template_path = $this->_get_template_path_from_node($template_node_id))
			{
				debug :: write_error('template node id not found',
		 			__FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
					array('template_node_id' => $template_node_id));

				return new failed_response();
			}
		}
		elseif(isset($_REQUEST['template_path']))
			$template_path = $_REQUEST['template_path'];
		
		if(!$source_file_path = resolve_template_source_file_name($template_path))
		{
			debug :: write_error('template not found',
	 			__FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
				array('template_path' => $template_path));

			return new failed_response();
		}
		
		$template_contents = file_get_contents($source_file_path);
		
		$this->view->set('template_path', $template_path);
		$this->view->set('template_content', $this->_process_template_content($template_contents));
		
		return new response();
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
  	$parser =& new XML_HTMLSax();
  	
  	$handler =& new template_highlight_handler();
  	
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