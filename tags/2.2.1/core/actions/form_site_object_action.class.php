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
require_once(LIMB_DIR . 'core/lib/util/complex_array.class.php');
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/model/site_object_factory.class.php');
require_once(LIMB_DIR . 'core/model/search/full_text_indexer.class.php');

class form_site_object_action extends form_action
{
	var $object = null;
	
	var $site_object_class_name = '';
	
	var $datamap = array();

	var $indexer = null;
	
	function form_site_object_action($name='', $merge_definition=array())
	{
		$this->site_object_class_name = $this->_define_site_object_class_name();
		
		$this->object =& $this->get_site_object();
		
		$this->datamap = $this->_define_datamap();

		$this->indexer =& $this->_get_site_object_indexer();

		parent :: form_action($name);
	}
	
	function _define_site_object_class_name()
	{
	  return 'site_object';
	}

	function _define_datamap()
	{
	  return array(
			'parent_node_id' => 'parent_node_id',
			'identifier' => 'identifier',
			'title' => 'title'
	  );
	}

	function & _get_site_object_indexer()
	{
		return new full_text_indexer();
	}
	
	function get_datamap()
	{
		return $this->datamap;
	}	
	
	function & get_site_object()
	{
		return site_object_factory :: create($this->site_object_class_name);
	}	
}
?>