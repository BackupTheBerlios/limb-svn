<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_photo_action.class.php 21 2004-02-29 18:59:25Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');

class create_photo_action extends form_create_site_object_action
{
	function create_photo_action()
	{
		$definition = array(
			'site_object' => 'photogallery_object',
			'datamap' => array(
				'annotation' => 'annotation',
				'image_id' => 'image_id',
			)
		);
		
		parent :: form_create_site_object_action('photo_form', $definition);
	}
}

?>