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
  var $url = null;

  function StatsReferer()
  {
    $toolkit =& Limb :: toolkit();
    $this->db_table =& $toolkit->createDBTable('StatsRefererUrl');
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
    $rs =& $this->db_table->select("referer_url='" . $uri . "'");
    if ($referer_data = $rs->getRow())
      return $referer_data['id'];
    else
      return false;
  }

  function _insertRefererRecord($uri)
  {
    return $this->db_table->insert(array('id' => null, 'referer_url' => $uri));
  }

  function cleanUrl($raw_url)
  {
    $this->url->parse($raw_url);

    $this->url->removeQueryItem('PHPSESSID');

    return $this->url->toString(array('protocol', 'user', 'password', 'host', 'port', 'path', 'query'));
  }
}

?>