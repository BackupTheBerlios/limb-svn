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
require_once(LIMB_DIR . '/class/core/actions/action.class.php');

class recover_version_action extends action
{
	public function perform($request, $response)
	{
		if($request->has_attribute('popup'))
		  $response->write(close_popup_no_parent_reload_response());
	
	  $request->set_status(request :: STATUS_FAILURE);
	  
		if(!$version = $request->get('version'))
			return;

		if(!$node_id = $request->get('version_node_id'))
	    return;

    $datasource = Limb :: toolkit()->createDatasource('single_object_datasource');
    $datasource->set_node_id($node_id);

		if(!$site_object = wrap_with_site_object($datasource->fetch()))
			return;
		
		if(!is_subclass_of($site_object, 'content_object'))
			return;

		if(!$site_object->recover_version((int)$version))
		  return;

		if($request->has_attribute('popup'))
		  $response->write(close_popup_response($request));
	
	  $request->set_status(request :: STATUS_SUCCESS);
	}
}

?>