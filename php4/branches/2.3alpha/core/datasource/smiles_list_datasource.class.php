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
require_once(LIMB_DIR . '/core/datasource/datasource.class.php');
require_once(LIMB_DIR . '/core/model/chat/smiles.class.php');

class smiles_list_datasource extends datasource
{
	function & get_dataset(&$counter, $params=array())
	{
		$smiles =& new smiles();
		return new array_dataset($smiles->get_smiles_array());
	}
}


?>
