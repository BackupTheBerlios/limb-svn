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
interface data_finder
{
  public function find($params = array(), $sql_params = array());//refactor!!!
  
  public function count($sql_params=array());//refactor!!!
  
  public function find_by_id($id);
}

?>
