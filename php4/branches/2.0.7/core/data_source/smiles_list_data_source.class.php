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
require_once(LIMB_DIR . 'core/data_source/data_source.class.php');
require_once(LIMB_DIR . 'core/model/chat/smiles.class.php');

class smiles_list_data_source extends data_source
{
	function smiles_list_data_source()
	{
		parent :: data_source();
	}

	function & get_data_set(&$counter, $params=array())
	{
		$smiles =& new smiles();
		return new array_dataset($smiles->get_smiles_array());
	}
}


?>
