<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
interface DataFinder
{
  function find($params = array(), $sql_params = array());//refactor!!!

  function findCount($sql_params=array());//refactor!!!

  function findById($id);
}

?>
