<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: form_create_site_object_action.class.php 570 2004-02-26 12:37:31Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/util/complex_array.class.php');
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/model/site_object_factory.class.php');

class form_site_object_action extends form_action
{
	var $object = null;
	
	var $definition = array(
		'site_object' => 'site_object',
		'datamap' => array()
	);
	
	function form_site_object_action($name='', $merge_definition=array())
	{
		$this->definition = complex_array :: array_merge($this->definition, $merge_definition);
		
		$this->object =& $this->get_site_object();
		
		parent :: form_action($name);
	}
		
	function get_definition()
	{
		return $this->definition;
	}	
	
	function & get_site_object()
	{
		return site_object_factory :: create($this->definition['site_object']);
	}	
}
?>