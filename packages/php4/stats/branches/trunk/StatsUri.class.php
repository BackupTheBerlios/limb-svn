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
  var $url = null;

  function StatsUri()
  {
    $toolkit =& Limb :: toolkit();
    $this->db_table =& $toolkit->createDBTable('StatsUri');

    $this->url = new Uri();
  }

  function getUriId()
  {
    $uri = $this->cleanUrl($this->_getHttpUri());

    if ($result = $this->_getExistingUriRecordId($uri))
      return $result;

    return $this->_insertUriRecord($uri);
  }

  function _getHttpUri()
  {
    return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
  }

  function _getExistingUriRecordId($uri)
  {
    $rs =& $this->db_table->select(array("uri" => $uri));
    if ($uri_data = $rs->getRow())
      return $uri_data['id'];
    else
      return false;
  }

  function _insertUriRecord($uri)
  {
    return $this->db_table->insert(array('id' => null, 'uri' => $uri));
  }

  function cleanUrl($raw_url)
  {
    $this->url->parse($raw_url);

    $this->url->removeQueryItems();

    if($this->_isInnerUrl())
      return $this->url->toString(array('path', 'query'));
    else
      return $this->url->toString(array('protocol', 'user', 'password', 'host', 'port', 'path', 'query'));
  }

  function _isInnerUrl()
  {
    return ($this->url->getHost() == preg_replace('/^([^:]+):?.*$/', '\\1', $_SERVER['HTTP_HOST']));
  }
}

?>