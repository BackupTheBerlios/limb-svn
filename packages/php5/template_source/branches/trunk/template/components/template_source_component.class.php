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
class template_source_component extends component
{
	public function get_current_template_source_link()
	{
		if(!$site_object = wrap_with_site_object(fetcher :: instance()->fetch_requested_object()))
			return '';

		$site_object_controller = $site_object->get_controller();

		if(($action = $site_object_controller->get_action()) === false)
			return '';

		if(!$template_path = $site_object_controller->get_action_property($action, 'template_path'))
			return '';
		
		return '/root/template_source?t[]=' . $template_path;
	}
} 

?>