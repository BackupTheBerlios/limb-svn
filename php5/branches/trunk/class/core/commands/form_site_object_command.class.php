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
	
	function __construct()
	{
		$this->site_object_class_name = $this->_define_site_object_class_name();
		
		$this->object = $this->get_site_object();
		
		parent :: __construct();
	}
	
	protected function _define_site_object_class_name()
	{
	  return 'site_object';
	}

	public function get_site_object()
	{
		return Limb :: toolkit()->createSiteObject($this->site_object_class_name);
	}	
}
?>