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
require_once(LIMB_DIR . '/class/lib/http/Uri.class.php');

class StatsUri
{
  protected $db = null;
  protected $url = null;

  public function __construct()
  {
    $this->db = Limb :: toolkit()->getDB();
    $this->url = new Uri();
  }

  public function getUriId()
  {
    $uri = $this->cleanUrl($this->_getHttpUri());

    if ($result = $this->_getExistingUriRecordId($uri))
      return $result;

    return $this->_insertUriRecord($uri);
  }

  protected function _getHttpUri()
  {
    return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
  }

  protected function _getExistingUriRecordId($uri)
  {
    $this->db->sqlSelect('sys_stat_uri', '*',
      "uri='" . $uri . "'");
    if ($uri_data = $this->db->fetchRow())
      return $uri_data['id'];
    else
      return false;
  }

  protected function _insertUriRecord($uri)
  {
    $this->db->sqlInsert('sys_stat_uri',
      array('id' => null, 'uri' => $uri));
    return $this->db->getSqlInsertId('sys_stat_uri');
  }

  public function cleanUrl($raw_url)
  {
    $this->url->parse($raw_url);

    $this->url->removeQueryItems();

    if($this->_isInnerUrl())
      return $this->url->toString(array('path', 'query'));
    else
      return $this->url->toString(array('protocol', 'user', 'password', 'host', 'port', 'path', 'query'));
  }

  protected function _isInnerUrl()
  {
    return ($this->url->getHost() == preg_replace('/^([^:]+):?.*$/', '\\1', $_SERVER['HTTP_HOST']));
  }
}

?>