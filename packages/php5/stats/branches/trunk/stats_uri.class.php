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
require_once(LIMB_DIR . '/class/lib/http/uri.class.php');

class stats_uri
{
  protected $db = null;
  protected $url = null;

  public function __construct()
  {
    $this->db = Limb :: toolkit()->getDB();
    $this->url = new uri();
  }

  public function get_uri_id()
  {
    $uri = $this->clean_url($this->_get_http_uri());

    if ($result = $this->_get_existing_uri_record_id($uri))
      return $result;

    return $this->_insert_uri_record($uri);
  }

  protected function _get_http_uri()
  {
    return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
  }

  protected function _get_existing_uri_record_id($uri)
  {
    $this->db->sql_select('sys_stat_uri', '*',
      "uri='" . $uri . "'");
    if ($uri_data = $this->db->fetch_row())
      return $uri_data['id'];
    else
      return false;
  }

  protected function _insert_uri_record($uri)
  {
    $this->db->sql_insert('sys_stat_uri',
      array('id' => null, 'uri' => $uri));
    return $this->db->get_sql_insert_id('sys_stat_uri');
  }

  public function clean_url($raw_url)
  {
    $this->url->parse($raw_url);

    $this->url->remove_query_items();

    if($this->_is_inner_url())
      return $this->url->to_string(array('path', 'query'));
    else
      return $this->url->to_string(array('protocol', 'user', 'password', 'host', 'port', 'path', 'query'));
  }

  protected function _is_inner_url()
  {
    return ($this->url->get_host() == preg_replace('/^([^:]+):?.*$/', '\\1', $_SERVER['HTTP_HOST']));
  }
}

?>