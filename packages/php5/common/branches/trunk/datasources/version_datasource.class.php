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
require_once(LIMB_DIR . 'class/datasources/datasource.interface.php');

class version_datasource implements datasource
{
	public function get_dataset(&$counter, $params=array())
	{
		$counter = 0;

	  $request = request :: instance();

    if (!$version = $request->get('version'))
      return new empty_dataset();

    if (!$node_id = $request->get('version_node_id'))
      return new empty_dataset();

		$version = (int)$version;
		$node_id = (int)$node_id;

		if(!$site_object = wrap_with_site_object(fetcher :: instance()->fetch_one_by_node_id($node_id)))
			return new empty_dataset();

		if(!is_subclass_of($site_object, 'content_object'))
			return new empty_dataset();

		if(($version_data = $site_object->fetch_version($version)) === false)
			return new empty_dataset();

		$result = array();

		foreach($version_data as $attrib => $value)
		{
			$data['attribute'] = $attrib;
			$data['value'] = $value;
			$result[] = $data;
		}

		return new array_dataset($result);
	}
}


?>
