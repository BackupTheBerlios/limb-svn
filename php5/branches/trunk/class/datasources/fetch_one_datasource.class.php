<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/datasource/datasource.interface.php');

class fetch_one_datasource implements datasource
{
	public function get_dataset(&$counter, $params)
	{
		$item = array();

		if (isset($params['path']))
			$item = Limb :: toolkit()->getFetcher()->fetch_one_by_path($params['path']);

		return new array_dataset(array($item));
	}
}
?>