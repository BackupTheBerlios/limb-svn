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

class StatsReferer
{
  var $db = null;
  var $url = null;

  function StatsReferer()
  {
    $toolkit =& Limb :: toolkit();
    $this->db =& $toolkit->getDB();
    $this->url = new Uri();
  }

  function getRefererPageId()
  {
    if(!$clean_uri = $this->_getCleanRefererPage())
      return -1;

    if($this->_isInnerUrl())
      return -1;

    if ($result = $this->_getExistingRefererRecordId($clean_uri))
      return $result;

    return $this->_insertRefererRecord($clean_uri);
  }

  function _isInnerUrl()
  {
    return ($this->url->getHost() == preg_replace('/^([^:]+):?.*$/', '\\1', $_SERVER['HTTP_HOST']));
  }

  function _getCleanRefererPage()
  {
    if ($referer = $this->_getHttpReferer())
      return $this->cleanUrl($referer);

    return false;
  }

  function _getHttpReferer()
  {
    return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
  }

  function _getExistingRefererRecordId($uri)
  {
    $this->db->sqlSelect('sys_stat_referer_url', '*',
      "referer_url='" . $uri . "'");
    if ($referer_data = $this->db->fetchRow())
      return $referer_data['id'];
    else
      return false;
  }

  function _insertRefererRecord($uri)
  {
    $this->db->sqlInsert('sys_stat_referer_url',
      array('id' => null, 'referer_url' => $uri));
    return $this->db->getSqlInsertId('sys_stat_referer_url');
  }

  function cleanUrl($raw_url)
  {
    $this->url->parse($raw_url);

    $this->url->removeQueryItem('PHPSESSID');

    return $this->url->toString(array('protocol', 'user', 'password', 'host', 'port', 'path', 'query'));
  }
}

?>