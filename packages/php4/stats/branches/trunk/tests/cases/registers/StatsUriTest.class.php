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
require_once(LIMB_STATS_DIR . '/registers/StatsUri.class.php');

Mock :: generatePartial
(
  'StatsUri',
  'StatsUriSelfTestVersion',
  array(
    '_getHttpUri'
  )
);

class StatsUriTest extends LimbTestCase
{
  var $stats_uri = null;
  var $db = null;
  var $conn = null;

  function StatsUriTest()
  {
    parent :: LimbTestCase('stats uri test');
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $this->conn =& $toolkit->getDbConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('stats_uri');
  }

  function testGetId()
  {
    $uri = new Uri($url = 'http://test.com/path');
    $stats_uri = new StatsUri();

    $id = $stats_uri->getId($uri);

    $rs =& $this->db->select('stats_uri');
    $arr = $rs->getArray();
    $record = current($arr);

    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($record['id'], $id);
    $this->assertEqual($record['uri'], $url);
  }

  function testSameUri()
  {
    $uri = new Uri($url = 'http://test.com/path');
    $stats_uri = new StatsUri();

    $id1 = $stats_uri->getId($uri);
    $id2 = $stats_uri->getId($uri);

    $rs =& $this->db->select('stats_uri');

    $this->assertEqual($rs->getRowCount(), 1);
  }
}

?>