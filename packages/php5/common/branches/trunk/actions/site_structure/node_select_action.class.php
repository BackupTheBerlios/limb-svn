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

class node_select_action extends action
{
	public function perform($request, $response)
	{
	  $request->set_status(request :: STATUS_DONT_TRACK);

	  if(!$path = $request->get('path'))
	    return;

    $datasource = Limb :: toolkit()->getDatasource('single_object_datasource');
    $datasource->set_path($path);
    
	  if(!$object_data = $datasource->fetch())
	    return;

	  session :: set('limb_node_select_working_path', $path);
	  $dataspace = $this->view->find_child('parent_node_data');

	  $dataspace->import($object_data);
	}
}

?>