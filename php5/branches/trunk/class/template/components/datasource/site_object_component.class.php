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
require_once(LIMB_DIR . '/class/template/component.class.php');

class site_object_component extends component
{
	public function fetch_by_path($path)
	{
    $datasource = Limb :: toolkit()->createDatasource('single_object_datasource');
    $datasource->set_path($path);
		$this->import($datasource->fetch());
	}
		
	public function fetch_requested()
	{
    $datasource = Limb :: toolkit()->createDatasource('requested_object_datasource');
    $request = Limb :: toolkit()->getRequest();
    $datasource->set_request($request);
		$this->import($datasource->fetch());
	}
} 

?>