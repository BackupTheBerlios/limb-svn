<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: photogallery_folder.class.php 21 2004-02-29 18:59:25Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');

class paragraphs_list_page extends site_object
{
	function paragraphs_list_page()
	{
		parent :: site_object();
	}

	function _define_class_properties()
	{
		return  array(
			'class_ordr' => 0,
			'can_be_parent' => 1,
			'controller_class_name' => 'paragraphs_list_page_controller',
			'icon' => '/shared/images/folder.gif'
		);
	}

}

?>