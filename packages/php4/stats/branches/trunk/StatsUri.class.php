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
require_once(LIMB_DIR . '/core/http/Uri.class.php');

class StatsUri
{
  var $db_table = null;
  var $uri = null;

  function StatsUri()
  {
    $toolkit =& Limb :: toolkit();
    $this->db_table =& $toolkit->createDBTable('StatsUri');
  }

  function getId(& $uri)
  {
    $this->uri =& $uri;

    if ($record = $this->_getExistingUriRecord())
      return $record->get('id');

    return $this->_insertUriRecord();
  }

  function _getExistingUriRecord()
  {
    $rs =& $this->db_table->select(array("uri" => $this->uri->toString()));
    $rs->rewind();

    if ($rs->valid())
      return $rs->current();
  }

  function _insertUriRecord()
  {
    return $this->db_table->insert(array('id' => null, 'uri' => $this->uri->toString()));
  }
}

?>