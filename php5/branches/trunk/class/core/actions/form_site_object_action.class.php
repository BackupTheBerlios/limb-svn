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
require_once(LIMB_DIR . 'class/lib/util/complex_array.class.php');
require_once(LIMB_DIR . 'class/core/actions/form_action.class.php');
require_once(LIMB_DIR . 'class/core/site_objects/site_object_factory.class.php');
require_once(LIMB_DIR . 'class/search/full_text_indexer.class.php');

abstract class form_site_object_action extends form_action
{
	protected $object = null;
	
	protected $site_object_class_name = '';
	
	protected $datamap = array();

	protected $indexer = null;
	
	function __construct()
	{
		$this->site_object_class_name = $this->_define_site_object_class_name();
		
		$this->object = $this->get_site_object();
		
		$this->datamap = $this->_define_datamap();

		$this->indexer = $this->_get_site_object_indexer();

		parent :: __construct();
	}
	
	protected function _define_site_object_class_name()
	{
	  return 'site_object';
	}

	protected function _define_datamap()
	{
	  return array(
			'parent_node_id' => 'parent_node_id',
			'identifier' => 'identifier',
			'title' => 'title'
	  );
	}

	protected function _get_site_object_indexer()
	{
		return new full_text_indexer();
	}
	
	public function get_datamap()
	{
		return $this->datamap;
	}	
	
	public function get_site_object()
	{
		return site_object_factory :: create($this->site_object_class_name);
	}	
}
?>