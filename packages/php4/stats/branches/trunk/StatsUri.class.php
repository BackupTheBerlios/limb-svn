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
  var $db = null;
  var $url = null;

  function StatsUri()
  {
    $toolkit =& Limb :: toolkit();
    $this->db =& $toolkit->getDB();

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
    $this->db->sqlSelect('sys_stat_uri', '*',
      "uri='" . $uri . "'");
    if ($uri_data = $this->db->fetchRow())
      return $uri_data['id'];
    else
      return false;
  }

  function _insertUriRecord($uri)
  {
    $this->db->sqlInsert('sys_stat_uri',
      array('id' => null, 'uri' => $uri));
    return $this->db->getSqlInsertId('sys_stat_uri');
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