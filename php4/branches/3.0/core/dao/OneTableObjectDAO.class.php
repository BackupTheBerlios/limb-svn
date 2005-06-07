<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: NewslineDAO.class.php 78 2005-06-01 05:31:45Z pachanga $
*
***********************************************************************************/

class OneTableObjectDAO // implements DAO, DAOById
{
  var $db_name;

  function OneTableObjectDAO($db_name)
  {
    $this->db_name = $db_name;
  }

  function & fetch()
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable($this->db_name);
    return $db_table->select();
  }

  function & fetchByID($id)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable($this->db_name);

    $result = new Dataspace();

    if(!$record =& $db_table->selectRecordById($id))
      return $result;

    return $record;
  }
}

?>
