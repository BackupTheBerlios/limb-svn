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
require_once(LIMB_DIR . 'core/lib/http/http_request.inc.php');
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_table_factory.class.php');

define('DEFAULT_SEARCH_ITEMS_PER_PAGE', 10);

class search_action extends form_action
{
	var $search_pager_id = 'search_pager';
	
	function search_action($name = 'search_form')
	{
		parent :: form_action($name);
	}
	
	function _init_dataspace()
	{
		if (isset($_REQUEST['search_query']))
			$this->dataspace->set('search_query', $_REQUEST['search_query']);

		if (isset($_REQUEST['limit']))
			$this->dataspace->set('limit', $_REQUEST['limit']);
	}
			
	function _valid_perform()
	{
		$_REQUEST['search_query'] = $this->dataspace->get('search_query');
	
		$this->_set_template_search_query();
		
		$items_per_page = $this->dataspace->get('limit');
		if (!$items_per_page)
			$items_per_page = DEFAULT_SEARCH_ITEMS_PER_PAGE;
			
		$this->_set_pager_items_per_page($items_per_page);
												
		return new response(RESPONSE_STATUS_FORM_SUBMITTED);
	}
		
	function _get_query()
	{		
		return $this->dataspace->get('search_query');
	}	
			
	function _set_pager_items_per_page($items_per_page)
	{
		if($pager =& $this->view->find_child($this->search_pager_id))
			$pager->set_items_per_page($items_per_page);
	}
		
	function _set_template_search_query()
	{
		$this->view->set('search_query', htmlspecialchars($this->_get_query()));
	}
}

?>