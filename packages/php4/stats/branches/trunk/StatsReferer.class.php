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

class StatsReferer
{
  var $db_table = null;
  var $uri = null;
  var $record = null;

  function StatsReferer()
  {
    $toolkit =& Limb :: toolkit();
    $this->db_table =& $toolkit->createDBTable('StatsRefererUrl');
  }

  function getId(&$uri)
  {
    $this->uri =& $uri;

    if($this->record)
      return $this->record->get('id');

    if($this->uri->toString() == '')
      return -1;

    if ($record =& $this->_getRefererRecord())
    {
      $this->record =& $record;
      return $this->record->get('id');
    }

    return $this->_insertRefererRecord();
  }

  function isRefererTo($uri, $base_uri)
  {
    if(($uri->getHost() != $base_uri->getHost()) ||
       ($uri->getPort() != $base_uri->getPort()) ||
       ($uri->getProtocol() != $base_uri->getProtocol()))
      return true;
    else
      return false;
  }

  function _getRefererRecord()
  {
    $rs =& $this->db_table->select("referer_url='" . $this->uri->toString() . "'");
    $rs->rewind();
    if($rs->valid())
      return $rs->current();
  }

  function _insertRefererRecord()
  {
    return $this->db_table->insert(array('referer_url' => $this->uri->toString()));
  }

  function cleanUrl($raw_url)
  {
    $uri = new Uri($raw_url);

    $uri->removeQueryItem('PHPSESSID');

    return $uri->toString(array('protocol', 'user', 'password', 'host', 'port', 'path', 'query'));
  }
}

?>